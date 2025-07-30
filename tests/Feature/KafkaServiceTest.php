<?php
use Tests\TestCase;
use OpenNebel\LaravelKafka\KafkaService;

uses(Tests\TestCase::class);

it('can produce a JSON message', function () {
    $kafka = app(KafkaService::class);

    $message = [
        'type' => 'email',
        'to' => 'test@example.com',
        'subject' => 'Hello',
        'body' => 'Bienvenue à MondialGP !'
    ];

    expect(fn() => $kafka->produceJson('test-topic', $message))->not()->toThrow();
});