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
        Schema::create('models', function (Blueprint $table) {
            $table->id();
            $table->string('model')->unique();
            $table->string('SKU')->unique();
            $table->string('scanned_image')->nullable();
            $table->string('website_image')->nullable();
            $table->string('stars')->nullable();
            $table->string('source')->nullable();
            $table->string('first_production')->nullable();
            $table->string('semi_or_no')->nullable();
            $table->decimal('average_of_stones', 10, 2)->nullable();
            // $table->string('to_print')->default('');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('models');
    }
};
