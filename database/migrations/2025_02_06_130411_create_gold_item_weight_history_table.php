<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('gold_item_weight_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gold_item_id');
            $table->decimal('weight_before', 8, 2);
            $table->decimal('weight_after', 8, 2);
            $table->timestamps();
    
            $table->foreign('gold_item_id')->references('id')->on('gold_items')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gold_item_weight_history');
    }
};
