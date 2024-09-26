<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gold_item_id');
            $table->string('from_shop_name');
            $table->string('to_shop_name');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('gold_item_id')->references('id')->on('gold_items')->onDelete('cascade');
            $table->foreign('from_shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('to_shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_requests');
    }
}
