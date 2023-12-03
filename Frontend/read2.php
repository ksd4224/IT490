<?php

require 'vendor/autoload.php'; // Include the Composer autoloader

use PhpAmqpLib\Connection\AMQPStreamConnection;
session_start();

// RabbitMQ server connection parameters
$rabbitMQHost = '10.147.17.79'; // Change this to your RabbitMQ server IP address
$rabbitMQPort = 5672; // Default RabbitMQ port
$rabbitMQUser = 'backend';
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

    // Set a timeout of 30 seconds (adjust as needed)
    $timeout = 60;
    $start_time = time();

    // Callback function to process received messages
    $callback = function ($message) {
        // Store the received message in the session
        $_SESSION['received_message'] = $message->body;

        echo 'Received: ', $message->body, "\n";

        // Check if the first word of the received message is 'success'
        $messageWords = explode(' ', $message->body);
        if (!empty($messageWords) && $messageWords[0] === 'Success') {
            // Redirect the user to the home screen or any desired location
            header("Location: test.php");
            exit(); // Ensure script termination after the header redirection
        }
    };

    // Consume messages from the queue
    $channel->basic_consume($queueName, '', false, true, false, false, $callback);

    // Wait for messages or until the timeout is reached
    while (count($channel->callbacks) && (time() - $start_time) < $timeout) {
        $channel->wait(null, false, $timeout);
    }

    $channel->close();
    $connection->close();

    // Check if the timeout occurred
    if ((time() - $start_time) >= $timeout) {
        echo "Timeout: No messages received after $timeout seconds.\n";
        // You can add additional logic here if needed
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


