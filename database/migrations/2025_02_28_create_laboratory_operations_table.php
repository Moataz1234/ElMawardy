<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laboratory_operations', function (Blueprint $table) {
            $table->id();
            $table->string('operation_number')->unique();  // رقم العملية
            $table->date('operation_date')->nullable();               // تاريخ العملية
            $table->decimal('total_input_weight', 10, 3)->default(0);   // إجمالي الوزن الداخل
            $table->decimal('total_output_weight', 10, 3)->default(0);  // إجمالي الوزن الخارج
            $table->decimal('loss', 10, 3)->default(0);   // الخسية (الفرق)
            $table->decimal('silver_weight', 10, 3)->default(0);   // وزن الفضة
            $table->decimal('operation_cost', 10, 2)->default(0);  // تكلفة العملية
            $table->string('status')->default('active');   // حالة العملية
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laboratory_operations');
    }
}; 