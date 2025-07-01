# Laravel Kafka

**Laravel Kafka** est une librairie simple et fluide pour produire des messages vers Apache Kafka à partir de vos applications Laravel. Elle repose sur l’extension [php-rdkafka](https://github.com/arnaud-lb/php-rdkafka) et suit les conventions Laravel (Service Provider + Facade).

> Maintenu par [OpenNebel](https://github.com/opennebel)

---

## 🚀 Installation

Assurez-vous que l'extension PHP `rdkafka` est installée sur votre machine :

```bash
pecl install rdkafka
````

Puis, installez la librairie via Composer :

```bash
composer require opennebel/laravel-kafka
```

---

## ⚙️ Configuration

Publiez le fichier de configuration :

```bash
php artisan vendor:publish --tag=config
```

Et configurez `.env` :

```env
KAFKA_BROKERS=localhost:29092
KAFKA_DEFAULT_TOPIC=notification-events
```

---

## 🧪 Utilisation

### Utilisation simple avec la façade

```php
use OpenNebel\LaravelKafka\Facades\Kafka;

Kafka::produce('notification-events', 'Hello Kafka!');
```

### Exemple avec un message JSON structuré

```php
$data = [
    'type' => 'email',
    'to' => 'nebel.mass@gmail.com',
    'subject' => 'Bienvenue chez MondialGP',
    'template' => 'welcome-email',
    'variables' => [
        'name' => 'Jean Dupont',
        'supportEmail' => 'support@mondialgp.com'
    ]
];

Kafka::produce('notification-events', json_encode($data));
```

---

## 🧩 Injection de dépendance (au lieu de la façade)

```php
use OpenNebel\LaravelKafka\KafkaService;

public function handle(KafkaService $kafka)
{
    $kafka->produce('notification-events', 'Message direct via service');
}
```

---

## 🛠 Exemple de configuration (`config/kafka.php`)

```php
return [
    'brokers' => env('KAFKA_BROKERS', 'localhost:9092'),
    'default_topic' => env('KAFKA_DEFAULT_TOPIC', 'notification-events'),
];
```

---

## 📦 Versions & stabilité

* PHP >= 8.0
* Laravel >= 9.x
* Kafka broker requis (local ou distant)
* Compatible avec Docker & environnement de prod

---

## 📄 Licence

MIT © OpenNebel

