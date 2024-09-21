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
        Schema::create('gold_items', function (Blueprint $table) {
            $table->id();
            $table->string('link');
            $table->string('serial_number');
            $table->string('shop_name');
            $table->unsignedBigInteger('shop_id');
            $table->string('kind');
            $table->string('model');
            $table->boolean('talab');
            $table->string('gold_color');
            $table->string('stones');
            $table->string('metal_type');
            $table->string('metal_purity');
            $table->integer('quantity');
            $table->float('weight');
            $table->date('rest_since');
            $table->string('source');
            $table->boolean('to_print')->default(false);
            $table->decimal('price', 10, 2);
            $table->string('semi_or_no');
            $table->float('average_of_stones');
            $table->float('net_weight');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gold_items');
    }
};
