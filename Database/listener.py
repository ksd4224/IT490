import json
import pika
import logging
import mysql.connector
from datetime import datetime

# Import other scripts
from registration import handle_registration, connect_to_database as reg_connect_to_database
from login import handle_login, connect_to_database as login_connect_to_database
from forgot import handle_forgot_password
from meals import handle_meals, connect_to_database as meals_connect_to_database
from goals import handle_goals, connect_to_database as goals_connect_to_database
from weight import handle_weight, connect_to_database as weight_connect_to_database
from forum import handle_forum, connect_to_database as forum_connect_to_database



class DateTimeEncoder(json.JSONEncoder):
    def default(self, o):
        if isinstance(o, datetime):
            return o.isoformat()
        return super().default(o)

logging.basicConfig(level=logging.INFO)
logging.getLogger('pika').setLevel(logging.WARNING)
logging.getLogger('urllib3').setLevel(logging.WARNING)

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

def setup_queues(channel):
    registration_request_queue = 'back-reg-request'
    login_request_queue = 'back-login-request'
    forgot_password_request_queue = 'back-pass-request'
    meals_request_queue = 'back-meals-request'
    goals_request_queue = 'your_goals_queue'  # Replace with the actual queue name for goals
    weight_request_queue = 'back-weight-request'  # New queue for weight
    forum_request_queue = 'your_forum_queue'  # Replace with the actual forum queue name

    channel.queue_declare(queue=registration_request_queue, durable=True)
    channel.queue_declare(queue=login_request_queue, durable=True)
    channel.queue_declare(queue=forgot_password_request_queue, durable=True)
    channel.queue_declare(queue=meals_request_queue, durable=True)
    channel.queue_declare(queue=goals_request_queue, durable=True)
    channel.queue_declare(queue=weight_request_queue, durable=True)  # Declare the new weight queue
    channel.queue_declare(queue=forum_request_queue, durable=True)  # Declare the forum queue

    return (
        registration_request_queue,
        login_request_queue,
        forgot_password_request_queue,
        meals_request_queue,
        goals_request_queue,
        weight_request_queue,  # Include the new weight queue
        forum_request_queue  # Include the forum queue
    )

if __name__ == "__main__":
    # Connect to RabbitMQ
    connection, channel = connect_to_rabbitmq()

    # Connect to the registration database
    reg_db_connection, reg_cursor = reg_connect_to_database()

    # Connect to the login database
    login_db_connection, login_cursor = login_connect_to_database()

    # Connect to the meals database
    meals_db_connection, meals_cursor = meals_connect_to_database()

    # Connect to the goals database
    goals_db_connection, goals_cursor = goals_connect_to_database()

    # Connect to the weight database
    weight_db_connection, weight_cursor = weight_connect_to_database()

    # Connect to the forum database
    forum_db_connection, forum_cursor = forum_connect_to_database()

    # Set up queues
    reg_queue, login_queue, forgot_password_queue, meals_request_queue, goals_request_queue, weight_request_queue, forum_request_queue = setup_queues(channel)  # Include the forum queue

    # Set up the consumer for registration queue
    try:
        channel.basic_consume(
            queue=reg_queue,
            on_message_callback=lambda ch, method, properties, body: handle_registration(
                json.loads(body), reg_cursor, reg_db_connection, channel),
            auto_ack=True
        )

        # Set up the consumer for login queue
        channel.basic_consume(
            queue=login_queue,
            on_message_callback=lambda ch, method, properties, body: handle_login(
                ch, method, properties, body, login_cursor, login_db_connection, channel),
            auto_ack=True
        )

        # Set up the consumer for forgot password queue
        channel.basic_consume(
            queue=forgot_password_queue,
            on_message_callback=lambda ch, method, properties, body: handle_forgot_password(
                ch, method, properties, body, login_cursor, login_db_connection, channel),
            auto_ack=True
        )

        # Set up the consumer for meals request queue
        channel.basic_consume(
            queue=meals_request_queue,
            on_message_callback=lambda ch, method, properties, body: handle_meals(
                json.loads(body), meals_cursor, meals_db_connection, channel),
            auto_ack=True
        )

        # Set up the consumer for goals request queue
        channel.basic_consume(
            queue=goals_request_queue,
            on_message_callback=lambda ch, method, properties, body: handle_goals(
                json.loads(body), goals_cursor, goals_db_connection, channel),
            auto_ack=True
        )

        # Set up the consumer for weight request queue
        channel.basic_consume(
            queue=weight_request_queue,
            on_message_callback=lambda ch, method, properties, body: handle_weight(
                ch, method, properties, body, weight_cursor, weight_db_connection, channel),
            auto_ack=True
        )

        # Set up the consumer for forum request queue
        channel.basic_consume(
            queue=forum_request_queue,
            on_message_callback=lambda ch, method, properties, body: handle_forum(
                json.loads(body), forum_cursor, forum_db_connection, channel),
            auto_ack=True
        )

        print('Waiting for messages. To exit press CTRL+C')
        channel.start_consuming()

    except KeyboardInterrupt:
        print('Interrupted. Closing connection.')
        channel.stop_consuming()

    finally:
        connection.close()
        reg_db_connection.close()
        login_db_connection.close()
        meals_db_connection.close()
        goals_db_connection.close()
        weight_db_connection.close()
        forum_db_connection.close()

