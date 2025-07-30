<?php

namespace OpenNebel\LaravelKafka\Factory;

use RdKafka\Conf;

class KafkaConfigFactory
{
    public static function fromLaravelConfig(): Conf
    {
        $conf = new Conf();
        $brokers = config('kafka.brokers');

        if (!is_string($brokers) || empty($brokers)) {
            throw new \InvalidArgumentException("Invalid kafka.brokers config");
        }

        $conf->set('metadata.broker.list', $brokers);

        foreach (config('kafka.options', []) as $key => $value) {
            $conf->set($key, $value);
        }

        return $conf;
    }
}