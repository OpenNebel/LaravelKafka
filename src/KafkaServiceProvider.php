<?php
namespace OpenNebel\LaravelKafka;

use Illuminate\Support\ServiceProvider;

class KafkaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kafka.php', 'kafka');

        $this->app->singleton(KafkaService::class, fn() => new KafkaService());
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/kafka.php' => config_path('kafka.php'),
        ], 'laravel-kafka-config');
    }
}
