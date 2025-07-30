<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Kafka Brokers
    |--------------------------------------------------------------------------
    |
    | List of Kafka brokers to connect to. Default: localhost:9092
    | You can also specify multiple brokers: "localhost:9092,localhost:29092"
    |
    */

    // Address of Kafka brokers. Can be set via the KAFKA_BROKERS environment variable.
    // Default is "localhost:9092".
    'brokers' => env('KAFKA_BROKERS', 'localhost:9092'),

    /*|--------------------------------------------------------------------------
    | Kafka Options
    |--------------------------------------------------------------------------
    | | Additional configuration options for the Kafka producer.
    | You can set any valid RdKafka configuration option here.
    |
    */
    'options' => [],

    /*
    |--------------------------------------------------------------------------
    | Default Topic
    |--------------------------------------------------------------------------
    |
    | If you want to produce to a single topic without redefining it each time,
    | you can set it here. Otherwise, you can always pass it as a parameter.
    |
    */

    // Default Kafka topic name. Can be set via the KAFKA_DEFAULT_TOPIC environment variable.
    // Default is "notification-events".
    'default_topic' => env('KAFKA_DEFAULT_TOPIC', 'notification-events'),

    /*
    |--------------------------------------------------------------------------
    | Configuration for Asynchronous Operations
    |--------------------------------------------------------------------------
    |
    | Defines parameters for Kafka asynchronous operations, such as
    | enabling async mode and the queue used.
    |
    */

    'async' => [
        // Enables or disables Kafka asynchronous operations.
        'enabled' => env('KAFKA_ASYNC_ENABLED', true),

        // Name of the queue used for asynchronous operations.
        'queue' => env('KAFKA_ASYNC_QUEUE', 'default'),
    ],

];