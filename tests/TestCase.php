<?php

namespace Tests;

use OpenNebel\LaravelKafka\KafkaServiceProvider;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            KafkaServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('kafka.brokers', 'localhost:9092');
        $app['config']->set('kafka.default_topic', 'notifications-events');
    }
}
