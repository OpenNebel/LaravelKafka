<?php

namespace OpenNebel\LaravelKafka\Factory;

use RdKafka\Producer;
use RdKafka\Conf;

class KafkaProducerFactory
{
    public static function handle(Conf $conf): Producer
    {
        return new Producer($conf);
    }
}
