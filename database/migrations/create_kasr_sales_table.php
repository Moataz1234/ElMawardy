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
        Schema::create('kasr_sales', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('shop_name')->nullable();
            // $table->foreign('shop_name')
            //     ->references('shop_name')
            //     ->on('users')
            //     ->onDelete('cascade');
            $table->string('kind')->nullable();
            $table->decimal('weight', 10, 2);
            $table->string('metal_purity');
            $table->string('item_type')->default('customer');
            // $table->string('metal_type')->default('gold');
            $table->string('image_path')->nullable();
            $table->decimal('offered_price', 12, 2)->nullable();
            $table->date('order_date')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, rejected
            // $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_sales');
    }
}; 