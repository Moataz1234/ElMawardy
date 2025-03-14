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
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gold_item_id')->nullable()->constrained('gold_items');
            $table->foreignId('pound_id')->nullable()->constrained('gold_pounds_inventory');
            $table->string('from_shop_name');
            $table->string('to_shop_name');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->enum('type', ['item', 'pound'])->default('item');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_requests');
    }
};
