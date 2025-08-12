<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ferry_trips', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('depart_time');
            $table->string('origin');
            $table->string('destination');
            $table->unsignedInteger('capacity');
            $table->decimal('price',8,2);
            $table->timestamps();
        });

        Schema::create('ferry_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ferry_trip_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->enum('status',['unpaid','paid','refunded'])->default('paid');
            $table->decimal('total_amount',10,2)->default(0);
            $table->string('code')->unique();
            $table->string('qr_path')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('ferry_tickets');
        Schema::dropIfExists('ferry_trips');
    }
};
