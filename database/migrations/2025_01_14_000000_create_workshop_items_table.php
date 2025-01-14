<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workshop_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('transferred_by');
            $table->string('serial_number');
            $table->string('shop_name');
            $table->string('kind');
            $table->string('model');
            $table->string('gold_color');
            $table->string('metal_purity');
            $table->decimal('weight', 8, 2);
            $table->text('transfer_reason')->nullable();
            $table->timestamp('transferred_at')->useCurrent();
            $table->timestamps();

            $table->foreign('model')
                ->references('model')
                ->on('models')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_items');
    }
};
