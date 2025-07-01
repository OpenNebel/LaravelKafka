<?php

namespace OpenNebel\LaravelKafka\Facades;

use Illuminate\Support\Facades\Facade;

class Kafka extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \OpenNebel\LaravelKafka\KafkaService::class;
    }
}
