<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('sale_requests', function (Blueprint $table) {
            $table->id();
            $table->string('item_serial_number');
            $table->string('related_item_serial');
            $table->index('related_item_serial');
            $table->string('item_type')->nullable();
            $table->string('weight')->nullable();
            $table->string('purity')->nullable();
            $table->string('kind')->nullable();
            $table->string('shop_name');
            $table->string('approver_shop_name')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('customer_id');
            $table->decimal('price', 10, 2);  // Add price column
            $table->timestamp('sold_date')->nullable();
            $table->string('payment_method')->nullable();
            
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_requests');
    }
};
