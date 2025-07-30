<?php

namespace OpenNebel\LaravelKafka\Console\Commands;

use Illuminate\Console\Command;
use OpenNebel\LaravelKafka\KafkaService;
use OpenNebel\LaravelKafka\Models\KafkaFailedMessage;

class KafkaRetryFailed extends Command
{
    protected $signature = 'kafka:retry-failed';
    protected $description = 'Retry failed Kafka messages from the DLQ';

    public function handle(KafkaService $kafka): int
    {
        $messages = KafkaFailedMessage::all();

        foreach ($messages as $message) {
            try {
                if ($message->as_json) {
                    $kafka->produceJson($message->topic, json_decode($message->payload, true));
                } else {
                    $kafka->produce($message->topic, $message->payload);
                }
                $message->delete();
                $this->info("Retried and removed message ID {$message->id}");
            } catch (\Throwable $e) {
                $this->error("Failed again for message ID {$message->id}: " . $e->getMessage());
            }
        }

        return self::SUCCESS;
    }
}