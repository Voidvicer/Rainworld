<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('ferry_trips', function(Blueprint $table){
            $table->boolean('blocked')->default(false)->after('price');
        });
    }
    public function down(): void {
        Schema::table('ferry_trips', function(Blueprint $table){
            $table->dropColumn('blocked');
        });
    }
};
