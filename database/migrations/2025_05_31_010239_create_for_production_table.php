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
        Schema::create('for_production', function (Blueprint $table) {
            $table->id();
            $table->string('model')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('not_finished')->nullable();
            $table->string('gold_color')->nullable();
            $table->date('order_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('for_production');
    }
};
