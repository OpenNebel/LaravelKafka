<?php

namespace OpenNebel\LaravelKafka;

use InvalidArgumentException;
use OpenNebel\LaravelKafka\Factory\KafkaConfigFactory;
use OpenNebel\LaravelKafka\Factory\KafkaProducerFactory;
use OpenNebel\LaravelKafka\Jobs\ProduceKafkaMessage;
use RdKafka\Exception;
use RdKafka\Producer;
use RdKafka\Metadata;
use RuntimeException;
use Throwable;

class KafkaService
{
    protected Producer $producer;

    public function __construct()
    {
        $conf = KafkaConfigFactory::fromLaravelConfig();
        $this->producer = KafkaProducerFactory::handle($conf);
    }

    /**
     * Sends a raw string message to a Kafka topic.
     *
     * @param string $topicName The name of the Kafka topic.
     * @param string $message The raw message payload to be sent.
     * @param array{
     *   key?: string|null,
     *   headers?: array<string, string>|null,
     *   partition?: int,
     *   flag?: int
     * } $options
     * Optional parameters:
     *     - Key: Message key used for partitioning.
     *     - headers: Kafka headers (associative array).
     *     - partition: Partition number (default: RD_KAFKA_PARTITION_UA).
     *     - Flag: Kafka message flag (default: 0).
     *
     * @throws RuntimeException If flushing fails after multiple attempts.
     * @throws Exception If Kafka production fails.
     */
    public function produce(
        string $topicName,
        string $message,
        array  $options = []
    ): void
    {
        $topic = $this->producer->newTopic($topicName);

        $partition = $options['partition'] ?? RD_KAFKA_PARTITION_UA;
        $flag = $options['flag'] ?? 0;
        $key = $options['key'] ?? null;
        $headers = $options['headers'] ?? null;

        if ($headers !== null && !is_array($headers)) {
            throw new InvalidArgumentException('Kafka headers must be an associative array or null.');
        }
        if ($key !== null && !is_string($key)) {
            throw new InvalidArgumentException('Kafka key must be a string or null.');
        }

        $topic->producev(
            $partition,
            $flag,
            $message,
            $key,
            $headers
        );

        $this->producer->poll(0);

        for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
            $result = $this->producer->flush(10000);
            if ($result === RD_KAFKA_RESP_ERR_NO_ERROR) return;
        }

        throw new RuntimeException('Unable to flush Kafka messages.');
    }

    /**
     * Sends a message to Kafka after encoding it as a JSON string.
     *
     * @param string $topicName The name of the Kafka topic.
     * @param array $payload The associative array to encode and send.
     * @param array $options Optional parameters for message production:
     *
     * @throws InvalidArgumentException|Exception if JSON encoding fails.
     */
    public function produceJson(string $topicName, array $payload, array $options = []): void
    {
        $json = json_encode($payload);

        if ($json === false || json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(
                'Failed to encode payload as JSON: ' . json_last_error_msg()
            );
        }

        $this->produce($topicName, $json, $options);
    }

    /**
     * Sends a raw message to the default topic defined in the configuration.
     *
     * @param string $message
     * @param array $options
     * @throws Exception
     */
    public function produceToDefault(string $message, array $options = []): void
    {
        $this->produce(config('kafka.default_topic'), $message, $options);
    }

    /**
     * Sends a JSON-encoded payload to the default topic defined in the configuration.
     *
     * @param array $payload
     * @param array $options
     * @throws Exception
     */
    public function produceJsonToDefault(array $payload, array $options = []): void
    {
        $this->produceJson(config('kafka.default_topic'), $payload, $options);
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
     * @throws Exception
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
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Asynchronously produces a message to a Kafka topic.
     *
     * @param string $topic The Kafka topic to produce to.
     * @param string|array $payload The message payload can be a string or an array.
     * @param array $options Optional parameters for message production:
     *     - Key: Message key used for partitioning (optional).
     *     - headers: Kafka headers (associative array, optional).
     *     - partition: Partition number (default: RD_KAFKA_PARTITION_UA).
     *     - Flag: Kafka message flag (default: 0).
     * @param bool $asJson Whether to encode the payload as JSON (default: true).
     */
    public function produceAsync(
        string $topic,
        string|array $payload,
        array $options = [],
        bool $asJson = true,
    ): void {
        ProduceKafkaMessage::dispatch($topic, $payload, $options, $asJson)
            ->onQueue(config('kafka.async.queue', 'default'));
    }

    /**
     * Asynchronously produces a message to the default Kafka topic.
     *
     * @param string|array $payload The message payload can be a string or an array.
     * @param array $options Optional parameters for message production.
     * @param bool $asJson Whether to encode the payload as JSON (default: true).
     */
    public function produceAsyncToDefault(
        string|array $payload,
        array $options = [],
        bool $asJson = true,
    ): void {
        $this->produceAsync(config('kafka.default_topic'), $payload, $options, $asJson);
    }
}