<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('lat',10,7);
            $table->decimal('lng',10,7);
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('active')->default(true);
            $table->string('image_url')->nullable();
            $table->string('scope')->default('global');
            $table->nullableMorphs('promotable');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('locations');
    }
};
