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
        Schema::create('gold_items_sold', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->nullable();
            $table->string('model')->nullable();
            $table->string('shop_name')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->string('kind')->nullable();
            $table->float('weight')->nullable();
            $table->string('gold_color')->nullable();
            $table->string('metal_type')->nullable();
            $table->string('metal_purity')->nullable();
            $table->integer('quantity')->nullable();
            $table->date('add_date')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->date('sold_date')->nullable();
            $table->string('stones')->nullable();
            $table->boolean('talab')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable(); // Add customer_id column
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade'); // Define foreign key relationship  
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
         Schema::table('gold_items_sold', function (Blueprint $table) {
            $table->dropForeign(['customer_id']); // Drop foreign key constraint
        });
        Schema::dropIfExists('gold_items_sold');
    }
};