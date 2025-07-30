<?php

namespace OpenNebel\LaravelKafka\Jobs;

use OpenNebel\LaravelKafka\KafkaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use OpenNebel\LaravelKafka\Models\KafkaFailedMessage;
use RdKafka\Exception;
use Throwable;

class ProduceKafkaMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $topic,
        public array|string $payload,
        public bool $asJson = true
    ) {}

    /**
     * @throws Throwable
     * @throws Exception
     */
    public function handle(KafkaService $kafka): void
    {
        try {
            if ($this->asJson && is_array($this->payload)) {
                $kafka->produceJson($this->topic, $this->payload);
            } else {
                $kafka->produce($this->topic, (string) $this->payload);
            }
        } catch (Throwable $e) {
            Log::error('Kafka Produce Failed', [
                'topic' => $this->topic,
                'payload' => $this->payload,
                'error' => $e->getMessage(),
            ]);

            KafkaFailedMessage::create([
                'topic' => $this->topic,
                'payload' => is_array($this->payload) ? json_encode($this->payload) : $this->payload,
                'as_json' => $this->asJson,
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}