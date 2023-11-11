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
from django.contrib.auth import authenticate

connection = pika.BlockingConnection(parameters)

channel = connection.channel()
channel.queue_declare(queue='login', durable=True)

def callback(ch, method, properties, body):
    data = json.loads(body.decode('utf-8'))
    email = data.get('email')
    psw = data.get('psw')

    print("Received data:", data)

    if not email or not password:
        print("Improper input: Email and Password must be provided")
        return

    if '@' not in email:
        print("Improper Input: Invalid email format.")
        return

    user = authenticate(email=email, psw=psw)

    if user is not None:
        print("User successfully logged in.")
    else:
        print("User account doesn't exist or incorrect credentials.")



channel.basic_consume(
        queue='login', on_message_callback=callback, auto_ack=True)

print(' [*] Waiting for messages. To exit press CTRL+C')
channel.start_consuming()
