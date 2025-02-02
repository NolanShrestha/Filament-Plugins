<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            
            $table->nullableMorphs('subject', 'subject');
            $table->nullableMorphs('causer', 'causer');
            
            $table->string('event')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->json('properties')->nullable();
            
            $table->timestamps();
            $table->index('log_name');
            $table->index('batch_uuid');
            $table->index('event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};