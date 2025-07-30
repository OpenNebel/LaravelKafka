<?php

namespace OpenNebel\LaravelKafka\Models;

use Illuminate\Database\Eloquent\Model;

class KafkaFailedMessage extends Model
{
    protected $fillable = [
        'topic',
        'payload',
        'as_json',
        'error_message',
    ];
}