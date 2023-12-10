import mysql.connector
import json
import pika
import logging

logging.basicConfig(level=logging.INFO)
logging.getLogger('pika').setLevel(logging.WARNING)
logging.getLogger('urllib3').setLevel(logging.WARNING)

def connect_to_database():
    db_host = '10.147.17.44'
    db_port = 3306
    db_user = 'rp54'
    db_password = 'Patel@123'
    db_name = 'ShapeShift'

    db_connection = mysql.connector.connect(
        host=db_host,
        port=db_port,
        user=db_user,
        password=db_password,
        database=db_name
    )

    cursor = db_connection.cursor()
    return db_connection, cursor

def connect_to_rabbitmq():
    rabbitmq_primary_host = '10.147.17.34'
    rabbitmq_secondary_host = '10.147.17.79'
    rabbitmq_port = 5672

    rabbitmq_hosts = [rabbitmq_primary_host, rabbitmq_secondary_host]

    connection = None
    channel = None

    for host in rabbitmq_hosts:
        rabbitmq_params = pika.ConnectionParameters(
            host=host,
            port=rabbitmq_port,
            credentials=pika.PlainCredentials(username='backend', password='password'),
        )

        try:
            connection = pika.BlockingConnection(rabbitmq_params)
            channel = connection.channel()
            break
        except Exception as e:
            logging.error(f"Connection to RabbitMQ failed for host {host}. Error: {e}")

    if not connection or not channel:
        logging.error("Unable to connect to any RabbitMQ host. Exiting.")
        exit()

    return connection, channel

def setup_login_queue(channel):
    login_request_queue_name = 'back-login-request'
    channel.queue_declare(queue=login_request_queue_name, durable=True)
    return login_request_queue_name

def handle_login(ch, method, properties, body, cursor, db_connection, channel):
    login_sql = "SELECT * FROM users WHERE email = %s AND password = %s"
    data = json.loads(body)
    cursor.execute(login_sql, (data['email'], data['password']))
    user = cursor.fetchone()

    response_message = {"status": "success" if user else "no_success"}

    if user:
        response_message["user_data"] = {
            "user_id": user[0],
            "email": user[1],
            "password": user[2],
            "weight": user[3],
            "height": user[4],
            "goal": user[5],
            "first_name": user[6],
            "last_name": user[7],
            "movie": user[8],
            "color": user[9]
        }

        # Fetch nutrition data using the correct column names and join
        nutrition_sql = """
            SELECT nd.*
            FROM nutrition_data nd
            JOIN meals m ON nd.meal_id = m.meal_id
            WHERE m.user_id = %s
        """
        cursor.execute(nutrition_sql, (user[0],))
        nutrition_data = cursor.fetchone()

        if not nutrition_data:
            # Insert default nutrition data if it doesn't exist
            insert_nutrition_sql = """
                INSERT INTO nutrition_data (meal_id, calories, protein, fat, carbohydrates, sugar, serving_size, servings)
                VALUES ((SELECT meal_id FROM meals WHERE user_id = %s LIMIT 1), 0, 0, 0, 0, 0, 0, 0)
            """
            cursor.execute(insert_nutrition_sql, (user[0],))
            db_connection.commit()

            # Fetch the inserted nutrition data
            cursor.execute(nutrition_sql, (user[0],))
            nutrition_data = cursor.fetchone()

        response_message["nutrition_data"] = nutrition_data

    # Publish the response message
    channel.basic_publish(
        exchange='',
        routing_key='login_response',
        body=json.dumps(response_message),
        properties=pika.BasicProperties(
            delivery_mode=2,
        )
    )
    print("Login response sent back to the 'login_response' queue.")

if __name__ == "__main__":
    connection, channel = connect_to_rabbitmq()

    queue_name = setup_login_queue(channel)
    logging.info(f"Declared queue: {queue_name}")

    db_connection, cursor = connect_to_database()

    try:
        channel.basic_consume(
            queue=queue_name,
            on_message_callback=lambda ch, method, properties, body: handle_login(ch, method, properties, body, cursor, db_connection, channel),
            auto_ack=True
        )
        logging.info('Waiting for messages. To exit press CTRL+C')
        channel.start_consuming()

    except KeyboardInterrupt:
        logging.info('Interrupted. Closing connection.')
        channel.stop_consuming()

    finally:
        connection.close()
        db_connection.close()
