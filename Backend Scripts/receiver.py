#!/usr/bin/env python3

import json
from shapeshift.models import User

try:
    import pika
except Exception as e:
    print("Some modules are missing {}".format(e))

credentials = pika.PlainCredentials('backend','password')
connection = pika.BlockingConnection(
        pika.ConnectionParameters(host='10.248.179.6', credentials = credentials))

channel = connection.channel()
channel.queue_declare(queue='front-back', durable=True)

def callback(ch, method, properties, body):
    data = json.loads(body)
    user = User(
        first_name=data['first'],
        last_name=data['last'],
        email=data['email'] ,
        password=data['psw'] 
    )
    user.save()
    print("Received data:", data)

channel.basic_consume(
        queue='front-back', on_message_callback=callback, auto_ack=True)

print(' [*] Waiting for messages. To exit press CTRL+C')
channel.start_consuming()



