# LaravelKafka

**LaravelKafka** is a simple yet powerful library for producing messages to Apache Kafka from your Laravel applications. It builds upon [php-rdkafka](https://github.com/arnaud-lb/php-rdkafka) and adheres to Laravel conventions, including Service Providers, Facades, Queueable Jobs, Artisan commands, dependency injection, and configuration.

> 🔧 Maintained by [OpenNebel](https://github.com/opennebel)

-----

## 🧩 Features

* ✅ **Synchronous** Kafka message sending
* 🔁 **Asynchronous** support via Laravel's queue system
* 🧱 Modular structure (producers, config, jobs, DLQ)
* 🛠 Error handling (Dead Letter Queue)
* 🧪 Integrated Artisan commands (`status`, `retry-failed`)
* 📦 Compatible with Laravel Horizon, Telescope, Docker, CI/CD

-----

## 🚀 Installation

### 1\. Install the PHP Kafka extension

```bash
pecl install rdkafka
```

> 📌 Ensure `php.ini` loads the extension:
> `extension=rdkafka`

### 2\. Install the library

```bash
composer require opennebel/laravel-kafka
```

-----

## ⚙️ Configuration

### Method 1 – Full Publication (config + migration)

```bash
php artisan vendor:publish --tag=laravel-kafka
```

### Method 2 – Separate Publication

```bash
# Only the config
php artisan vendor:publish --tag=laravel-kafka-config

# Only the DLQ migration
php artisan vendor:publish --tag=laravel-kafka-migrations
```

### `.env` File

```env
KAFKA_BROKERS=localhost:9092
KAFKA_DEFAULT_TOPIC=notification-events

# For asynchronous sending via Laravel Queue
KAFKA_ASYNC_ENABLED=true
KAFKA_ASYNC_QUEUE=default
```

-----

## ✉️ Sending Messages

### 🔹 Synchronous Sending

```php
use OpenNebel\LaravelKafka\Facades\Kafka;

// Raw message
Kafka::produce('notification-events', 'Hello Kafka');

// JSON message
Kafka::produceJson('notification-events', [
    'type' => 'email',
    'to' => 'user@example.com',
    'subject' => 'Welcome',
    'variables' => ['name' => 'John']
]);
```

### 🔸 Asynchronous Sending (Laravel Queue)

```php
Kafka::produceAsync('notification-events', [
    'type' => 'sms',
    'to' => '+33600000000',
    'message' => 'Your code is 1234'
]);

Kafka::produceAsyncToDefault([
    'type' => 'sms',
    'to' => '+33600000000',
    'message' => 'Your code is 1234'
]);
```

> Start the worker to process jobs:

```bash
php artisan queue:work
```

-----

## 🧩 Usage with Dependency Injection

```php
use OpenNebel\LaravelKafka\KafkaService;

public function handle(KafkaService $kafka)
{
    $kafka->produceToDefault('message via DI');
}
```

-----

## 💥 Error Handling (DLQ)

If `flush()` fails or the broker is unreachable, the message is:

* recorded in the `kafka_failed_messages` table
* accessible via Artisan
* manually re-attemptable

### Migration:

```bash
php artisan migrate
```

### Retry Failed Messages:

```bash
php artisan kafka:retry-failed
```

-----

## 🛠 Artisan Commands

| Command | Description |
| :------------------- | :---------------------------------------------------- |
| `kafka:status` | Checks Kafka broker connectivity and displays topics |
| `kafka:retry-failed` | Retries failed messages stored in the DLQ |

These commands are **automatically registered** via the `KafkaServiceProvider`.

-----

## 🧰 Service API

| Method | Description |
| :---------------------------- | :--------------------------------------------- |
| `produce($topic, $msg)` | Raw message to a given topic |
| `produceJson($topic, $data)` | JSON-encoded array to a given topic |
| `produceToDefault($msg)` | Raw message to the default topic |
| `produceJsonToDefault($data)` | JSON-encoded array to the default topic |
| `produceAsync(...)` | Adds the message to the queue (Laravel Job) |
| `flush($timeoutMs)` | Forces messages to be sent to the broker |
| `getQueueLength()` | Number of messages awaiting locally |
| `ping()` | Broker connection test |
| `getMetadata()` | Returns Kafka cluster info (brokers, topics) |
| `isConnected()` | Indicates if the producer is initialized |

-----

## 📂 Configuration (`config/kafka.php`)

```php
return [
    'brokers' => env('KAFKA_BROKERS', 'localhost:9092'),
    'default_topic' => env('KAFKA_DEFAULT_TOPIC', 'notification-events'),

    'async' => [
        'enabled' => env('KAFKA_ASYNC_ENABLED', true),
        'queue' => env('KAFKA_ASYNC_QUEUE', 'default'),
    ],

    'options' => [
        // Examples:
        // 'compression.codec' => 'snappy',
        // 'acks' => 'all',
    ],
];
```

-----

## ✅ Prerequisites

* PHP \>= 8.0
* Laravel \>= 9.x
* `rdkafka` PHP extension
* Operational Kafka Broker (local, Docker, cloud)

-----

## 🧠 Recommendations

* 🔄 Use Laravel Horizon for asynchronous job monitoring
* 🔍 Monitor errors via Laravel Telescope or Bugsnag
* 🚨 Enable Kafka logs (`storage/logs/laravel.log`) to inspect errors

-----

## 📄 License

MIT © [OpenNebel](https://github.com/opennebel)