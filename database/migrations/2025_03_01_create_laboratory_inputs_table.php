<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laboratory_inputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')
                  ->constrained('laboratory_operations')
                  ->onDelete('cascade');
            $table->integer('purity')->nullable();                    // عيار السبيكة
            $table->decimal('weight', 10, 3)->nullable();            // وزن السبيكة
            $table->date('input_date')->nullable();                  // تاريخ الإدخال
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laboratory_inputs');
    }
}; 