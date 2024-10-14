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
            $table->string('link')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('shop_name')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->string('kind')->nullable();
            $table->string('model')->nullable();
            $table->boolean('talab')->nullable();
            $table->string('gold_color')->nullable();
            $table->string('stones')->nullable();
            $table->string('metal_type')->nullable();
            $table->string('metal_purity')->nullable();
            $table->integer('quantity')->nullable();
            $table->float('weight')->nullable()->index();
            $table->date('rest_since')->nullable();
            $table->string('source')->nullable();
            $table->boolean('to_print')->default(false)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('semi_or_no')->nullable();
            $table->float('average_of_stones')->nullable();
            $table->float('net_weight')->nullable();
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
