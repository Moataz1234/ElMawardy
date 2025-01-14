<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workshop_transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('shop_name');
            $table->string('serial_number');
            $table->string('status')->default('pending');
            $table->text('reason')->nullable();
            $table->string('requested_by');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('gold_items')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_transfer_requests');
    }
};
