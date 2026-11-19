<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unique('user_id');
        });

        Schema::create('event_venues', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('venue_type', 30);
            $table->text('description');
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_venues');

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });
    }
};
