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
        Schema::create('deleted_items_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id'); // Remove ->foreign()->references()
            $table->string('deleted_by');
            $table->string('serial_number');
            $table->string('shop_name');
            $table->string('kind');
            $table->string('model');
            $table->string('gold_color');
            $table->string('metal_purity');
            $table->decimal('weight', 8, 2);
            $table->text('deletion_reason')->nullable();
            $table->timestamp('deleted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_items_history');
    }
};
