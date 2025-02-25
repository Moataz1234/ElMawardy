<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transfer_requests_history', function (Blueprint $table) {
            $table->id();
            // Transfer request data
            $table->string('from_shop_name');
            $table->string('to_shop_name');
            $table->string('status');
            
            // Gold item data at time of transfer
            $table->string('serial_number');
            $table->string('model')->nullable();
            $table->string('kind')->nullable();
            $table->decimal('weight', 10, 3)->nullable();
            $table->string('gold_color')->nullable();
            $table->string('metal_type')->nullable();
            $table->string('metal_purity')->nullable();
            $table->integer('quantity')->default(1);
            $table->text('stones')->nullable();
            $table->string('talab')->nullable();
            
            // Additional columns from Models table
            $table->integer('stars')->nullable();
            $table->string('scanned_image')->nullable();
            
            // Tracking dates
            $table->timestamp('transfer_completed_at');
            $table->timestamp('item_sold_at')->nullable();
            $table->timestamps();

            // Add indexes for common queries
            $table->index('serial_number');
            $table->index('from_shop_name');
            $table->index('to_shop_name');
            $table->index('transfer_completed_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transfer_requests_history');
    }
}; 