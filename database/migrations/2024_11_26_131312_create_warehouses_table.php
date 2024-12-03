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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('link')->nullable();
            $table->string('serial_number');
            $table->string('shop_name')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->string('kind');
            $table->string('model');
            $table->string('talab');
            $table->string('gold_color');
            $table->string('stones');
            $table->string('metal_type');
            $table->string('metal_purity');
            $table->integer('quantity');
            $table->decimal('weight', 8, 2);
            $table->date('rest_since');
            $table->string('source');
            $table->boolean('to_print')->default(false);
            $table->decimal('price', 10, 2);
            $table->string('semi_or_no');
            $table->decimal('average_of_stones', 8, 2);
            $table->decimal('net_weight', 8, 2);
            $table->string('website')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
