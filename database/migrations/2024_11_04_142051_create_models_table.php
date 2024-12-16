<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  
     public function up()
     {
         Schema::create('gold_pounds', function (Blueprint $table) {
             $table->id();
             $table->string('kind');
             $table->decimal('weight', 8, 2);
             $table->integer('purity');
             $table->integer('quantity')->default(1);
             $table->decimal('total_weight', 8, 2)->storedAs('weight * quantity')->nullable();
             $table->text('description')->nullable();
             $table->timestamps();
         });
 
         // Insert initial data
         DB::table('gold_pounds')->insert([
             ['kind' => 'one_pound_jorge', 'weight' => 8, 'purity' => 21, 'quantity' => 1, 'description' => ''],
             ['kind' => 'half_pound_jorge', 'weight' => 4, 'purity' => 21, 'quantity' => 1, 'description' => ''],
             ['kind' => 'quartar_pound_jorge', 'weight' => 2, 'purity' => 21, 'quantity' => 1, 'description' => ''],
             ['kind' => 'bizos1', 'weight' => 41.55, 'purity' => 21, 'quantity' => 1, 'description' => ''],
             ['kind' => 'bizos2', 'weight' => 40.55, 'purity' => 21, 'quantity' => 1, 'description' => ''],
             ['kind' => '5_pound', 'weight' => 26, 'purity' => 21, 'quantity' => 1, 'description' => ''],
             ['kind' => 'bar_1gm', 'weight' => 1, 'purity' => 24, 'quantity' => 1, 'description' => ''],
             ['kind' => 'bar_2.5gm', 'weight' => 2.5, 'purity' => 24, 'quantity' => 1, 'description' => ''],
             ['kind' => 'bar_5gm', 'weight' => 5, 'purity' => 24, 'quantity' => 1, 'description' => ''],
             ['kind' => 'bar_10gm', 'weight' => 10, 'purity' => 24, 'quantity' => 1, 'description' => ''],
             ['kind' => 'bar_15.55gm', 'weight' => 15.55, 'purity' => 24, 'quantity' => 1, 'description' => ''],
             ['kind' => 'bar_20gm', 'weight' => 20, 'purity' => 24, 'quantity' => 1, 'description' => ''],
             ['kind' => 'bar_31.1gm', 'weight' => 31.1, 'purity' => 24, 'quantity' => 1, 'description' => ''],
             ['kind' => 'bar_50gm', 'weight' => 50, 'purity' => 24, 'quantity' => 1, 'description' => ''],
             ['kind' => 'bar_100gm', 'weight' => 100, 'purity' => 24, 'quantity' => 1, 'description' => ''],
         ]);
     }
 
     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         Schema::dropIfExists('gold_pounds');
     }
 
};
