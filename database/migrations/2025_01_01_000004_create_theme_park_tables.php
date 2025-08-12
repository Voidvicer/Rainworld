<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('theme_park_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('visit_date');
            $table->unsignedInteger('quantity');
            $table->enum('status',['unpaid','paid','refunded'])->default('paid');
            $table->decimal('total_amount',10,2)->default(0);
            $table->string('code')->unique();
            $table->string('qr_path')->nullable();
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type',['ride','show','beach']);
            $table->text('description')->nullable();
            $table->decimal('base_price',8,2)->default(0);
            $table->unsignedBigInteger('location_id')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('activity_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('start_time');
            $table->string('end_time');
            $table->unsignedInteger('capacity');
            $table->timestamps();
        });

        Schema::create('activity_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_park_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_schedule_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->enum('status',['booked','canceled','completed'])->default('booked');
            $table->decimal('total_amount',10,2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('activity_bookings');
        Schema::dropIfExists('activity_schedules');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('theme_park_tickets');
    }
};
