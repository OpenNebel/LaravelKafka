<?php

namespace OpenNebel\LaravelKafka\Models;

use Illuminate\Database\Eloquent\Model;

class KafkaFailedMessage extends Model
{
    protected $fillable = [
        'topic',
        'payload',
        'as_json',
        'options',
        'error_message',
    ];

    protected $casts = [
        'options' => 'array',
        'as_json' => 'boolean',
    ];
}
