<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->decimal('discount_percentage', 5, 2)->nullable()->after('content');
        });
    }

    public function down()
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('discount_percentage');
        });
    }
};
