#!/usr/bin/env python3

import os
import django
import pika
import sys
import json

os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'IT490.settings')
credentials = pika.PlainCredentials('backend','password')
parameters = pika.ConnectionParameters(
    host='10.248.179.10', 
    port=5672, 
    credentials = credentials
 )

try:
    django.setup()

except Exception as e:
    print("Some modules are missing {}".format(e))
    

from users.models import CustomUser
from users.utils import save_user_info

connection = pika.BlockingConnection(parameters)

channel = connection.channel()
channel.queue_declare(queue='front-back', durable=True)

def callback(ch, method, properties, body):
    data = json.loads(body.decode('utf-8'))
    first_name = data.get('first')
    last_name = data.get('last')
    email = data.get('email')
    password = data.get('psw')
    print("Received data:", data)
    
    if not first_name or not last_name or not email or not password:
        print("Improper input: All fields must be provided.")
        return

    save_user_info(first_name, last_name, email, password)



channel.basic_consume(
        queue='front-back', on_message_callback=callback, auto_ack=True)

print(' [*] Waiting for messages. To exit press CTRL+C')
channel.start_consuming()



