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
        Schema::create('kasr_sales_complete', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_kasr_sale_id'); // Reference to the original kasr_sale id
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('original_shop_name')->nullable(); // Store the original shop name
            $table->string('shop_name')->default('rabea'); // Default to 'rabea' for completed kasr sales
            $table->string('image_path')->nullable();
            $table->decimal('offered_price', 12, 2)->nullable();
            $table->date('order_date')->nullable();
            $table->date('completion_date')->nullable(); // Date when it was moved to completed
            $table->string('status')->default('accepted');
            $table->timestamps();
            
            // Add foreign key constraint
            $table->foreign('original_kasr_sale_id')
                  ->references('id')
                  ->on('kasr_sales')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasr_sales_complete');
    }
}; 