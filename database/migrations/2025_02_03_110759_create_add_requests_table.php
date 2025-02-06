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
        Schema::create('add_requests', function (Blueprint $table) {
            $table->id();
            $table->string('model')->nullable();
            $table->string('serial_number')->unique();
            $table->string('kind')->nullable()->index();
            $table->string('shop_name')->nullable();
            $table->string('shop_id')->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->string('gold_color')->nullable();
            $table->string('metal_type')->nullable();
            $table->string('metal_purity')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('stones')->nullable();
            $table->boolean('talab')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->timestamps();

            $table->foreign('model')
                ->references('model')
                ->on('models')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_requests');
    }
};
