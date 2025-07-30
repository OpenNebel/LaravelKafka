<?php

namespace OpenNebel\LaravelKafka\Console\Commands;

use Illuminate\Console\Command;
use OpenNebel\LaravelKafka\KafkaService;
use RdKafka\Exception;

class KafkaStatus extends Command
{
    protected $signature = 'kafka:status';
    protected $description = 'Check Kafka connectivity and display broker metadata';

    /**
     * @throws Exception
     */
    public function handle(KafkaService $kafka): int
    {
        $this->info('Checking Kafka broker connectivity...');

        if (! $kafka->isConnected()) {
            $this->error('Kafka producer is not initialized.');
            return self::FAILURE;
        }

        if (! $kafka->ping()) {
            $this->error('❌ Kafka broker is not reachable.');
            return self::FAILURE;
        }

        $this->info("✅ Kafka broker is reachable.");

        $metadata = $kafka->getMetadata();

        $this->line("Brokers:");
        foreach ($metadata->getBrokers() as $broker) {
            $this->line("- ID: {$broker->getId()}, Host: {$broker->getHost()}, Port: {$broker->getPort()}");
        }

        $this->line("\nTopics:");
        foreach ($metadata->getTopics() as $topic) {
            $status = $topic->getErr() === RD_KAFKA_RESP_ERR_NO_ERROR ? 'ok' : 'error';
            $this->line("- {$topic->getTopic()} (\"{$status}\")");
        }

        return self::SUCCESS;
    }
}