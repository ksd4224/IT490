import os
import pika
import json
import pymysql
from datetime import datetime
from decimal import Decimal

class DateTimeEncoder(json.JSONEncoder):
    def default(self, o):
        if isinstance(o, datetime):
            return o.isoformat()
        elif isinstance(o, Decimal):
            return float(o)
        return super(DateTimeEncoder, self).default(o)

#credentials = pika.PlainCredentials('backend', 'password')
#parameters = pika.ConnectionParameters(
#    host='10.147.17.79',
    #host='10.147.17.34',
#    port=5672,
#    credentials=credentials)

primary_host = '10.147.17.79'
secondary_host = '10.147.17.34'
output_file = 'weight_data.txt'  # Specify your desired file name or path here

credentials = pika.PlainCredentials('backend', 'password')

def check_rabbitmq_host(host):
    try:
        parameters = pika.ConnectionParameters(host=host, port=5672, credentials=credentials)
        connection = pika.BlockingConnection(parameters)
        connection.close()
        return True
    except pika.exceptions.AMQPConnectionError:
        return False

if not check_rabbitmq_host(primary_host):
    print(f"Primary host {primary_host} is not running RabbitMQ. Setting secondary host {secondary_host}.")
    parameters = pika.ConnectionParameters(host=secondary_host, port=5672, credentials=credentials)
else:
    parameters = pika.ConnectionParameters(host=primary_host, port=5672, credentials=credentials)

# Create connections and channels
consume1_connection = pika.BlockingConnection(parameters)
consume1_channel = consume1_connection.channel()
consume1_channel.queue_declare(queue='back-weight-request', durable=True)
consume1_channel.queue_bind(exchange='backend-database', queue='back-weight-request', routing_key='weight.back')

def save_to_txt(data):
    with open(output_file, 'w') as file:
        file.write(json.dumps(data) + '\n')

def get_all_posts(cursor):
    post_sql = "SELECT user_id, first_name, post_content FROM posts"
    cursor.execute(post_sql)
    posts = cursor.fetchall()
    return posts

def get_mysql_connection():
    # Modify this with your MySQL connection details
    connection = pymysql.connect(
        host='10.147.17.44',
        user='rp54',
        password='Patel@123',
        database='ShapeShift',
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor
    )
    return connection, connection.cursor()

def get_user_totals(user_id, cursor):
    query = """
        SELECT SUM(calories) as total_calories,
               SUM(protein) as total_protein,
               SUM(carbohydrates) as total_carbohydrates,
               SUM(fat) as total_fat,
               SUM(sugar) as total_sugar
        FROM nutrition_data
        JOIN meals ON nutrition_data.meal_id = meals.meal_id
        WHERE meals.user_id = %s
    """
    cursor.execute(query, (user_id,))
    result = cursor.fetchone()
    return result

def callback_and_insert(ch, method, properties, body):
    data = json.loads(body.decode('utf-8'))
    print("Received data from backend:", data)

    try:
        email = data.get('email')
        weight = data.get('weight')
        print("Processing Data:", email, weight)

        # Connect to MySQL
        db_connection, cursor = get_mysql_connection()

        try:
            user_sql = "SELECT * FROM users WHERE email = %s"
            cursor.execute(user_sql, (email,))
            user = cursor.fetchone()

            if user:
                # Update weight information in the 'users' table
                update_weight_sql = "UPDATE users SET weight = %s WHERE email = %s"
                cursor.execute(update_weight_sql, (weight, email))
                db_connection.commit()
                print(f"Weight updated to {weight} for user {email}")

                # Fetch user totals
                user_totals = get_user_totals(user['user_id'], cursor)

                # Send success message back to the queue with user information, user_id, and user totals
                success_message = {
                    'status': 'success',
                    'message': f'Successfully updated weight for {email} to {weight}',
                    'user_data': {
                        'user_id': user['user_id'],
                        'email': user['email'],
                        'password': user['password'],
                        'weight': weight,
                        'height': user['height'],
                        'goal': user['goal'],
                        'first_name': user['first_name'],
                        'last_name': user['last_name'],
                    },
                    'user_totals': user_totals
                }
                all_posts = get_all_posts(cursor)
                success_message["all_posts"] = all_posts
                nutrition_sql = """
                    SELECT nd.*
                    FROM nutrition_data nd
                    JOIN meals m ON nd.meal_id = m.meal_id
                    WHERE m.user_id = %s
                """
                cursor.execute(nutrition_sql, (user[0],))
                nutrition_data = cursor.fetchall()
                success_message["nutrition_data"] = [
                    {
                        "data_id": row[0],
                        "meal_id": row[1],
                        "calories": row[2],
                        "protein": row[3],
                        "fat": row[4],
                        "carbohydrates": row[5],
                        "sugar": row[6],
                        "serving_size": row[7],
                        "servings": row[8]
                    } for row in nutrition_data
                ]

                meals_sql = "SELECT * FROM meals WHERE user_id = %s"
                cursor.execute(meals_sql, (user[0],))
                meals_data = cursor.fetchall()
                success_message["meals_data"] = [
                    {
                        "meal_id": row[0],
                        "user_id": row[1],
                        "meal_name": row[2],
                        "meal_datetime": row[3].isoformat() if row[3] else None
                    } for row in meals_data
                ]


                ch.basic_publish(
                    exchange='backend-database',
                    routing_key='weight.data',
                    body=json.dumps(success_message, cls=DateTimeEncoder),
                    properties=pika.BasicProperties(
                        delivery_mode=2,
                    )
                )

            else:
                print(f"User with email {email} not found in the database. Skipping weight update.")

        except Exception as e:
            print(f"Error reading and updating database: {e}")

        finally:
            db_connection.close()

        save_to_txt(data)

    except Exception as e:
        print(f"Error processing message: {e}")

if __name__ == "__main__":
    consume1_channel.basic_consume(queue='back-weight-request', on_message_callback=callback_and_insert, auto_ack=True)

    try:
        print('Weight is [*] Waiting for messages. To exit press CTRL+C')
        consume1_channel.start_consuming()

    except KeyboardInterrupt:
        consume1_channel.stop_consuming()

    consume1_connection.close()
