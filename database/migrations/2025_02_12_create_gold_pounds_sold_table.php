<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gold_pounds_sold', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gold_pound_id')->constrained('gold_pounds');
            $table->string('shop_name');
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('customer_id')->nullable(); // Add customer_id column
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade'); // Define foreign key relationship  
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('gold_pounds_sold');
    }
}; 