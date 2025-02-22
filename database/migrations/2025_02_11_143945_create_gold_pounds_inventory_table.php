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
    public function up(): void
    {
        Schema::create('gold_pounds_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gold_pound_id')->constrained('gold_pounds');
            $table->string('serial_number')->nullable();
            $table->string('related_item_serial')->nullable();
            $table->string('shop_name', 255)->nullable();  // Explicitly set the same length as users table
            $table->enum('type', ['standalone', 'in_item'])->default('standalone');
            $table->decimal('weight', 8, 2)->nullable();
            $table->integer('purity')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('status')->default('active');
            $table->timestamps();

            // Create the index and foreign key after all columns are defined
            // $table->index('shop_name');
            // $table->foreign('shop_name')
            //     ->references('shop_name')
            //     ->on('users')
            //     ->onDelete('cascade');
        });

        // Add conditional foreign key for serial_number
        // if (Schema::hasTable('gold_items')) {
        //     DB::statement('
        //         ALTER TABLE gold_pounds_inventory
        //         ADD CONSTRAINT check_serial_number
        //         FOREIGN KEY (serial_number)
        //         REFERENCES gold_items(serial_number)
        //         ON DELETE CASCADE
        //         WHERE type = "in_item"
        //     ');
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gold_pounds_inventory');
    }
};
