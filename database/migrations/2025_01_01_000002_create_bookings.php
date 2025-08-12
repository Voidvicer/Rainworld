<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedInteger('guests');
            $table->enum('status',['pending','confirmed','canceled','completed'])->default('confirmed');
            $table->decimal('total_amount',10,2)->default(0);
            $table->enum('payment_status',['unpaid','paid','refunded'])->default('paid');
            $table->string('confirmation_code')->unique();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('bookings'); }
};
