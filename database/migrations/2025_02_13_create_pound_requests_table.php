<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('add_pound_requests', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->foreignId('gold_pound_id')->constrained('gold_pounds');
            $table->string('shop_name')->nullable()->index();  // Add shop_name for standalone pounds
            // $table->foreign('shop_name')
            //     ->references('shop_name')
            //     ->on('users')
            //     ->onDelete('cascade');
            $table->enum('type', ['standalone', 'in_item']);
            $table->decimal('weight', 8, 2);
            $table->integer('quantity');
            $table->string('image_path')->nullable();
            $table->integer('custom_purity')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('add_pound_requests');
    }
};
