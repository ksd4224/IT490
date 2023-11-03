<?php

require 'vendor/autoload.php'; // Include the Composer autoloader

use PhpAmqpLib\Connection\AMQPStreamConnection;
session_start();

// RabbitMQ server connection parameters
$rabbitMQHost = '10.248.179.6'; //if we don’t get a static ip address this has to be changed to my ip everytime
$rabbitMQPort = 5672; // Default RabbitMQ port
$rabbitMQUser = 'frontend';
$rabbitMQPassword = 'password';
$virtualHost = '/';

// Queue name (frontend-to-backend-queue)
$queueName = 'back-front'; // Replace with the actual queue name

try {
    // Create a connection to RabbitMQ
    $connection = new AMQPStreamConnection($rabbitMQHost, $rabbitMQPort, $rabbitMQUser, $rabbitMQPassword, $virtualHost);

    // Create a channel
    $channel = $connection->channel();

    // Declare the queue
    list($queueName) = $channel->queue_declare($queueName, false, true, false, false);

    echo "Waiting for messages from the backend...\n";

    // Callback function to process received messages
    $callback = function ($message) {
        echo 'Received: ', $message->body, "\n";
    };

    //store in global variable
    $receivedMessage = $message->body;
    $_SESSION['received_message'] = $receivedMessage;

    // Consume messages from the queue
    $channel->basic_consume($queueName, '', false, true, false, false, $callback);

    // Wait for messages
    while (count($channel->callbacks)) {
        $channel->wait();
    }

    $channel->close();
    $connection->close();
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
