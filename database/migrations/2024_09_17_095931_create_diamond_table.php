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
        
        Schema::create('diamond', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();  // ID CODE
            $table->string('kind');  // Kind
            $table->decimal('cost', 10, 2)->nullable();  // Cost
            $table->string('calico1')->nullable();  // CALICO[1]
            $table->decimal('weight1', 10, 2)->nullable();  // WEIGHT[1]
            $table->string('calico2')->nullable();  // CALICO[2]
            $table->integer('number2')->nullable();  // NUMBER[2]
            $table->decimal('weight2', 10, 2)->nullable();  // WEIGHT[2]
            $table->string('calico3')->nullable();  // CALICO[3]
            $table->integer('number3')->nullable();  // NUMBER[3]
            $table->decimal('weight3', 10, 2)->nullable();  // WEIGHT[3]
            $table->string('calico4')->nullable();  // CALICO[4]
            $table->integer('number4')->nullable();  // NUMBER[4]
            $table->decimal('weight4', 10, 2)->nullable();  // WEIGHT[4]
            $table->string('calico5')->nullable();  // CALICO[5]
            $table->integer('number5')->nullable();  // NUMBER[5]
            $table->decimal('weight5', 10, 2)->nullable();  // WEIGHT[5]
            $table->string('calico6')->nullable();  // CALICO[6]
            $table->integer('number6')->nullable();  // NUMBER[6]
            $table->decimal('weight6', 10, 2)->nullable();  // WEIGHT[6]
            $table->string('sta')->nullable();  // STA
            $table->string('model')->nullable();  // MODEL
            $table->string('workshop')->nullable();  // WORKSHOP
            $table->string('tarkeeb')->nullable();  // TARKEEB
            $table->string('gela')->nullable();  // GELA
            $table->string('banue')->nullable();  // BANUE
            $table->date('date')->nullable();  // DATE
            $table->string('condition')->nullable();  // CONDITION
            $table->date('selling_date')->nullable();  // SELLING DATE
            $table->decimal('selling_price', 10, 2)->nullable();  // SELLING PRICE
            $table->string('shop')->nullable();  // SHOP
            $table->string('name')->nullable();  // NAME
            $table->string('return')->nullable();  // RETURN
            $table->date('date_r')->nullable();  // DATE. R
            $table->text('details')->nullable();  // DETAILS
            $table->decimal('cost1', 10, 2)->nullable();  // COST1
            $table->decimal('cost2', 10, 2)->nullable();  // COST2
            $table->decimal('cost3', 10, 2)->nullable();  // COST3
            $table->decimal('cost4', 10, 2)->nullable();  // COST4
            $table->decimal('cost5', 10, 2)->nullable();  // COST5
            $table->decimal('cost6', 10, 2)->nullable();  // COST6
            $table->string('certificate_code')->nullable();
            $table->timestamps();  // created_at and updated_at
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diamond');
    }
};
