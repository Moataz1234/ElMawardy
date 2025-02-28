<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laboratory_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')
                  ->constrained('laboratory_operations')
                  ->onDelete('cascade');
            $table->decimal('weight', 10, 3)->nullable();            // الوزن الخارج
            $table->integer('purity')->nullable();                    // عيار السبيكة
            $table->date('output_date')->nullable();                 // تاريخ الخروج
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laboratory_outputs');
    }
};