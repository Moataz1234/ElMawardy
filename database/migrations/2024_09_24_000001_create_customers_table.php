<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gold_items', function (Blueprint $table) {
            $table->id();
            $table->string('model')->nullable();
            // $table->string('talabat')->nullable();
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
            $table->string('status')->default('available');
            $table->date('rest_since')->nullable();
            $table->timestamps();

            $table->foreign('model')
            ->references('model')
            ->on('models')
            ->onDelete('cascade');
            
            // $table->foreign('talabat')->references('model')->on('talabat')->onDelete('cascade');
      
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gold_items');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
   
};
