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
            $table->string('shop_name');
            $table->string('approver_shop_name')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('customer_id');
            $table->decimal('price', 10, 2);  // Add price column

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
