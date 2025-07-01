<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Kafka Brokers
    |--------------------------------------------------------------------------
    |
    | Liste des brokers Kafka auxquels se connecter. Par défaut : localhost:9092
    | Tu peux aussi spécifier plusieurs brokers : "localhost:9092,localhost:29092"
    |
    */

    'brokers' => env('KAFKA_BROKERS', 'localhost:9092'),

    /*
    |--------------------------------------------------------------------------
    | Topic par défaut
    |--------------------------------------------------------------------------
    |
    | Si tu veux produire sur un topic unique sans le redéfinir à chaque fois,
    | tu peux le définir ici. Sinon, tu peux toujours le passer en paramètre.
    |
    */

    'default_topic' => env('KAFKA_DEFAULT_TOPIC', 'notification-events'),

];
