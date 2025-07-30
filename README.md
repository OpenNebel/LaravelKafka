# LaravelKafka

**LaravelKafka** is a simple yet powerful library for producing messages to Apache Kafka from your Laravel applications. It builds upon [php-rdkafka](https://github.com/arnaud-lb/php-rdkafka) and adheres to Laravel conventions, including Service Providers, Facades, Queueable Jobs, Artisan commands, dependency injection, and configuration.

> 🔧 Maintained by [OpenNebel](https://github.com/opennebel)

-----

## 🧩 Features

* ✅ **Synchronous** Kafka message sending
* 🔁 **Asynchronous** support via Laravel's queue system
* 🧱 Modular structure (producers, config, jobs, DLQ)
* 🧠 Support for headers, keys, partitions, msgflags
* 🛠 Error handling (Dead Letter Queue with full Kafka options)
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

// Raw message with options
Kafka::produce('notification-events', 'Hello Kafka', [
    'key' => 'user:123',
    'headers' => ['x-app' => 'mondialgp'],
    'partition' => 0,
    'flag' => 0
]);

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
], [
    'key' => 'job:456',
    'headers' => ['x-job' => 'welcome']
]);

Kafka::produceAsyncToDefault([
    'type' => 'sms',
    'to' => '+33600000000',
    'message' => 'Your code is 1234'
], [
    'key' => 'default-key'
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
    $kafka->produceToDefault('message via DI', [
        'headers' => ['x-di' => 'used']
    ]);
}
```

-----

## 💥 Error Handling (DLQ)

If `flush()` fails or the broker is unreachable, the message is:

* recorded in the `kafka_failed_messages` table
* all Kafka `options` are stored as JSON
* accessible via Artisan command
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

| Command              | Description                                         |
|:---------------------|:----------------------------------------------------|
| `kafka:status`       | Checks Kafka broker connectivity and lists metadata |
| `kafka:retry-failed` | Retries messages in the DLQ                         |

These commands are **auto-registered** via `KafkaServiceProvider`.

-----

## 🧰 Service API

| Method                    | Description                                                      |
|:--------------------------|:-----------------------------------------------------------------|
| `produce()`               | Raw string to topic with optional headers/key/partition/msgflags |
| `produceJson()`           | JSON payload to topic                                            |
| `produceToDefault()`      | Raw string to default topic                                      |
| `produceJsonToDefault()`  | JSON payload to default topic                                    |
| `produceAsync()`          | Dispatch async job to a topic                                    |
| `produceAsyncToDefault()` | Dispatch async job to default topic                              |
| `flush($timeoutMs)`       | Flush local queue to Kafka broker                                |
| `getQueueLength()`        | Messages in local Kafka buffer                                   |
| `ping()`                  | Kafka connection test                                            |
| `getMetadata()`           | Cluster info: topics, partitions, brokers                        |
| `isConnected()`           | Indicates producer was created correctly                         |

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
        // Kafka global options (passed to php-rdkafka Producer config)
        // e.g. 'compression.codec' => 'snappy'
    ],
];
```

-----

## ✅ Prerequisites

* PHP >= 8.0
* Laravel >= 9.x
* `ext-rdkafka` PHP extension
* Kafka broker (local, Docker, or cloud)

-----

## 🧠 Recommendations

* Use Laravel Horizon to manage async jobs
* Monitor failures with Telescope or Sentry
* Track Kafka logs via `storage/logs/laravel.log`

-----

## 📄 License

MIT © [OpenNebel](https://github.com/opennebel)