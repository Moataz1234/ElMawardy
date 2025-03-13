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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // Linking item with order
            $table->integer('quantity')->nullable(); // Corrected quantity field
            $table->string('order_kind')->nullable(); 
            $table->string('item_type')->nullable();
            $table->integer('ring_size')->nullable(); // Use integer for sizes
            $table->string('weight')->nullable();
            // $table->string('gold_color')->nullable();
            $table->string('image_link')->nullable();
            $table->string('order_details')->nullable();
            $table->string('order_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
