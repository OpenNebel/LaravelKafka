<?php

namespace OpenNebel\LaravelKafka;

use RdKafka\Conf;
use RdKafka\Producer;

class KafkaService
{
    protected Producer $producer;

    public function __construct()
    {
        $conf = new Conf();
        $conf->set('metadata.broker.list', config('kafka.brokers'));
        $this->producer = new Producer($conf);
    }

    public function produce(string $topicName, string $message): void
    {
        $topic = $this->producer->newTopic($topicName);
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
        $this->producer->poll(0);

        for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
            $result = $this->producer->flush(10000);
            if ($result === RD_KAFKA_RESP_ERR_NO_ERROR) return;
        }

        throw new \RuntimeException('Unable to flush Kafka messages.');
    }
}
