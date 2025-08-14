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
        Schema::table('ferry_tickets', function (Blueprint $table) {
            $table->text('qr_code')->nullable()->after('status');
            $table->timestamp('pass_issued_at')->nullable()->after('qr_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ferry_tickets', function (Blueprint $table) {
            $table->dropColumn(['qr_code', 'pass_issued_at']);
        });
    }
};
