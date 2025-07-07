<?php

namespace OpenNebel\LaravelKafka;

use RdKafka\Conf;
use RdKafka\Producer;
use RdKafka\Metadata;

class KafkaService
{
    protected Producer $producer;

    public function __construct()
    {
        $conf = new Conf();
        $conf->set('metadata.broker.list', config('kafka.brokers'));
        $this->producer = new Producer($conf);
    }

    /**
     * Sends a raw string message to a Kafka topic.
     *
     * @param string $topicName The name of the Kafka topic.
     * @param string $message The message payload to be sent.
     *
     * @throws \RuntimeException if the message cannot be flushed (sent) after several attempts.
     */
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

    /**
     * Sends a message to Kafka after encoding it as a JSON string.
     *
     * @param string $topicName The name of the Kafka topic.
     * @param array $payload The associative array to encode and send.
     *
     * @throws \InvalidArgumentException if JSON encoding fails.
     */
    public function produceJson(string $topicName, array $payload): void
    {
        $json = json_encode($payload);

        if ($json === false || json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(
                'Failed to encode payload as JSON: ' . json_last_error_msg()
            );
        }

        $this->produce($topicName, $json);
    }

    /**
     * Sends a raw message to the default topic defined in the configuration.
     *
     * @param string $message
     */
    public function produceToDefault(string $message): void
    {
        $this->produce(config('kafka.default_topic'), $message);
    }

    /**
     * Sends a JSON-encoded payload to the default topic defined in the configuration.
     *
     * @param array $payload
     */
    public function produceJsonToDefault(array $payload): void
    {
        $this->produceJson(config('kafka.default_topic'), $payload);
    }

    /**
     * Checks if the producer instance is initialized.
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->producer !== null;
    }

    /**
     * Manually flushes the Kafka message queue.
     *
     * @param int $timeoutMs
     * @return bool True if flush succeeded.
     */
    public function flush(int $timeoutMs = 10000): bool
    {
        return $this->producer->flush($timeoutMs) === RD_KAFKA_RESP_ERR_NO_ERROR;
    }

    /**
     * Returns the number of messages pending in the local queue.
     *
     * @return int
     */
    public function getQueueLength(): int
    {
        return $this->producer->getOutQLen();
    }

    /**
     * Retrieves metadata for the Kafka cluster.
     *
     * @return Metadata
     */
    public function getMetadata(): Metadata
    {
        return $this->producer->getMetadata(true, null, 10000);
    }

    /**
     * Pings the Kafka broker to check connectivity.
     *
     * @return bool True if broker is reachable.
     */
    public function ping(): bool
    {
        try {
            $this->producer->getMetadata(true, null, 1000);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}