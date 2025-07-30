<?php
namespace OpenNebel\LaravelKafka;

use Illuminate\Support\ServiceProvider;
use OpenNebel\LaravelKafka\Console\Commands\KafkaStatus;
use OpenNebel\LaravelKafka\Console\Commands\KafkaRetryFailed;

class KafkaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kafka.php', 'kafka');

        $this->app->singleton(KafkaService::class, fn() => new KafkaService());
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                KafkaStatus::class,
                KafkaRetryFailed::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/kafka.php' => config_path('kafka.php'),
        ], 'laravel-kafka-config');

        $this->publishes([
            __DIR__.'/../database/migrations/create_kafka_failed_messages_table.php' =>
                database_path('migrations/'.date('Y_m_d_His').'_create_kafka_failed_messages_table.php'),
        ], 'laravel-kafka-migrations');

        $this->publishes([
            __DIR__.'/../config/kafka.php' => config_path('kafka.php'),
            __DIR__.'/../database/migrations/create_kafka_failed_messages_table.php' =>
                database_path('migrations/'.date('Y_m_d_His').'_create_kafka_failed_messages_table.php'),
        ], 'laravel-kafka');
    }
}