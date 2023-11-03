<?php
require_once __DIR__ . '/vendor/autoload.php'; // Include the Composer autoloader

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Set up connection parameters
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

// Create a channel and declare the queue
$channel = $connection->channel();
$queueName = 'front-end user reg';
$channel->queue_declare($queueName, false, true, false, false);

echo ' [*] Waiting for messages. To exit, press Ctrl+C', PHP_EOL;

// Callback function to process received messages
$callback = function ($message) {
    echo ' [x] Received ', $message->body, PHP_EOL;
};

// Consume messages from the queue
$channel->basic_consume($queueName, '', false, true, false, false, $callback);

// Keep the script running to listen for messages
while (count($channel->callbacks)) {
    $channel->wait();
}

// Close the connection when done
$channel->close();
$connection->close();
?>
