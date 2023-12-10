# login.py
import mysql.connector
import json
import pika

def setup_login_queue(channel):
    login_request_queue_name = 'back-login-request'
    channel.queue_declare(queue=login_request_queue_name, durable=True)
    print(f"Declared queue: {login_request_queue_name}")
    return login_request_queue_name

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

def handle_login(data, cursor, db_connection, channel):
    email = data.get('email')
    password = data.get('password')

    if not email or not password:
        print("Missing email or password in login request.")
        # Assuming 'data-login-response' is the response queue
        channel.basic_publish(
            exchange='backend-database',
            routing_key='log.data',
            body=json.dumps({"status": "error", "message": "Missing email or password"}),
            properties=pika.BasicProperties(
                delivery_mode=2,
            )
        )
        return

    login_sql = "SELECT * FROM users WHERE email = %s AND password = %s"
    cursor.execute(login_sql, (email, password))
    user = cursor.fetchone()

    response_message = {"status": "success" if user else "failure"}

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
    else:
        response_message["message"] = "Invalid email or password"
        print("Login unsuccessful. Invalid email or password.")  # Add this line

    # Assuming 'data-login-response' is the response queue
    channel.basic_publish(
        exchange='backend-database',
        routing_key='log.data',
        body=json.dumps(response_message),
        properties=pika.BasicProperties(
            delivery_mode=2,
        )
    )
    print("Login response sent back to the 'data-login-response' queue.")

if __name__ == "__main__":
    connection = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
    channel = connection.channel()

    login_request_queue = setup_login_queue(channel)

    def on_message_callback(ch, method, properties, body):
        print("Received message:", body)
        handle_login(json.loads(body), cursor, db_connection, channel)

    channel.basic_consume(
        queue=login_request_queue,
        on_message_callback=on_message_callback,
        auto_ack=True
    )

    print('Waiting for login messages. To exit press CTRL+C')
    channel.start_consuming()

