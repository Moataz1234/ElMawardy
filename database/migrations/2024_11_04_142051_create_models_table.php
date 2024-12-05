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
            $table->string('category')->nullable();
            $table->string('source')->nullable();
            $table->date('first_production')->nullable();
            $table->string('semi_or_no')->nullable();
            $table->decimal('average_of_stones', 10, 2)->nullable();
            // $table->string('to_print')->default('');

            $table->timestamps();
        
            // Define the foreign key constraint
   // Optional: cascade delete if a category is deleted
        });

        // Add foreign key in the gold_items table
        Schema::table('gold_items', function (Blueprint $table) {
            $table->foreign('model')
                ->references('model')
                ->on('models')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gold_items', function (Blueprint $table) {
            $table->dropForeign(['model']);
        });

        Schema::dropIfExists('models');
        }
};
