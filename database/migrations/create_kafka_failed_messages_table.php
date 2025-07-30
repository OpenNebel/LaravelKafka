<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kafka_failed_messages', function (Blueprint $table) {
            $table->id();
            $table->string('topic');
            $table->longText('payload');
            $table->boolean('as_json');
            $table->json('options')->nullable();
            $table->text('error_message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kafka_failed_messages');
    }
};
