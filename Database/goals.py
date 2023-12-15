import mysql.connector
import json
import pika
import logging
from datetime import datetime

class DateTimeEncoder(json.JSONEncoder):
    def default(self, o):
        if isinstance(o, datetime):
            return o.isoformat()
        return super().default(o)

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
            print(f"Successfully connected to RabbitMQ host: {host}")
            break
        except Exception as e:
            print(f"Connection to RabbitMQ failed for host {host}. Error: {e}")
            print(f"Attempting the next host...")

    if not connection or not channel:
        print("Failed to connect to RabbitMQ. Please check your network connection and try again.")
        exit()

    return connection, channel

def setup_goals_queue(channel):
    goals_request_queue_name = 'back-goals-request'
    goals_response_queue_name = 'data-goals-response'

    channel.queue_declare(queue=goals_request_queue_name, durable=True)
    channel.queue_declare(queue=goals_response_queue_name, durable=True)

    return goals_request_queue_name, goals_response_queue_name

def get_user_workouts(user_id, cursor):
    query = """
        SELECT *
        FROM workout
        WHERE user_id = %s
    """
    cursor.execute(query, (user_id,))
    workout_data = cursor.fetchall()

    return [{"workout_id": row[0], "user_id": row[1], "workout_name": row[2], "created_at": row[3]} for row in workout_data]

def handle_goals(data, cursor, db_connection, channel):
    email = data.get('email')
    goal = data.get('goal')

    user_sql = "SELECT * FROM users WHERE email = %s"
    cursor.execute(user_sql, (email,))
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
        }

        workout_data = get_user_workouts(user[0], cursor)
        response_message["workout_data"] = workout_data

    response_message_json = json.dumps(response_message, cls=DateTimeEncoder)

    channel.basic_publish(
        exchange='backend-database',
        routing_key='goals.data',
        body=response_message_json,
        properties=pika.BasicProperties(
            delivery_mode=2,
        )
    )
    print("Goals response sent back to the 'goals.data' queue.")

if __name__ == "__main__":
    connection, channel = connect_to_rabbitmq()

    queue_name = setup_goals_queue(channel)
    logging.info(f"Declared queues: {queue_name}")

    db_connection, cursor = connect_to_database()

    try:
        channel.basic_consume(
            queue=queue_name[0],
            on_message_callback=lambda ch, method, properties, body: handle_goals(
                json.loads(body), cursor, db_connection, channel),
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

