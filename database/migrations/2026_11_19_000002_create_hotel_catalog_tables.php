<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('default_occupancy')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('operating_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_category_id')->constrained()->restrictOnDelete();
            $table->string('room_number', 30)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price_per_night', 10, 2);
            $table->unsignedSmallInteger('max_occupancy');
            $table->string('image_path')->nullable();
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();

            $table->index(['room_category_id', 'status']);
        });

        Schema::create('facility_room', function (Blueprint $table) {
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->primary(['facility_id', 'room_id']);
        });

        Schema::create('restaurant_menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('menu_type', 10)->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->decimal('price', 10, 2);
            $table->string('image_path')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->index(['menu_type', 'is_available']);
        });

        Schema::create('hotel_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_settings');
        Schema::dropIfExists('restaurant_menu_items');
        Schema::dropIfExists('facility_room');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('room_categories');
    }
};
