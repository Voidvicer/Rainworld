<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ferry_trips', function (Blueprint $table) {
            $table->enum('trip_type', ['departure', 'return'])->default('departure')->after('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ferry_trips', function (Blueprint $table) {
            $table->dropColumn('trip_type');
        });
    }
};
