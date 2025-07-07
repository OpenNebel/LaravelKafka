# Laravel Kafka

**Laravel Kafka** is a lightweight and expressive library to produce messages to Apache Kafka from your Laravel applications. It is built on top of [php-rdkafka](https://github.com/arnaud-lb/php-rdkafka) and follows Laravel conventions (Service Provider + Facade).

> Maintained by [OpenNebel](https://github.com/opennebel)

---

## 🚀 Installation

Make sure the PHP `rdkafka` extension is installed:

```bash
pecl install rdkafka
````

Then install the package via Composer:

```bash
composer require opennebel/laravel-kafka
```

---

## ⚙️ Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=laravel-kafka-config
```

Add the following to your `.env`:

```env
KAFKA_BROKERS=localhost:29092
KAFKA_DEFAULT_TOPIC=notification-events
```

---

## 🧪 Usage

### Basic usage with the Facade

```php
use OpenNebel\LaravelKafka\Facades\Kafka;

Kafka::produce('notification-events', 'Hello Kafka!');
```

### JSON message with structured payload

```php
$data = [
    'type' => 'email',
    'to' => 'nebel.mass@gmail.com',
    'subject' => 'Welcome to MondialGP',
    'template' => 'welcome-email',
    'variables' => [
        'name' => 'Jean Dupont',
        'supportEmail' => 'support@mondialgp.com'
    ]
];

Kafka::produceJson('notification-events', $data);
```

---

## 🧩 Dependency injection (instead of Facade)

```php
use OpenNebel\LaravelKafka\KafkaService;

public function handle(KafkaService $kafka)
{
    $kafka->produce('notification-events', 'Message sent via service');
}
```

---

## 🛠 Additional methods

| Method                   | Description                                           |
| ------------------------ | ----------------------------------------------------- |
| `produce()`              | Send a raw message to a given topic                   |
| `produceJson()`          | Send an array encoded as JSON to a given topic        |
| `produceToDefault()`     | Send a raw message to the default topic (from config) |
| `produceJsonToDefault()` | Send a JSON-encoded message to the default topic      |
| `isConnected()`          | Returns `true` if the producer is initialized         |
| `flush($timeoutMs)`      | Manually flush the message queue                      |
| `getQueueLength()`       | Returns number of messages waiting in the local queue |
| `getMetadata()`          | Returns full Kafka cluster metadata                   |
| `ping()`                 | Returns `true` if the broker is reachable             |

---

## ⚙️ Example config (`config/kafka.php`)

```php
return [
    'brokers' => env('KAFKA_BROKERS', 'localhost:9092'),
    'default_topic' => env('KAFKA_DEFAULT_TOPIC', 'notification-events'),
];
```

---

## 📦 Requirements

* PHP >= 8.0
* Laravel >= 9.x
* Apache Kafka broker required (local or remote)
* Compatible with Docker and production environments

---

## 📄 License

MIT © OpenNebel

```
