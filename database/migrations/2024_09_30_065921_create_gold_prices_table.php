
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('gold_prices', function (Blueprint $table) {
            $table->id();
            $table->decimal('gold_buy', 10, 2)->nullable();
            $table->decimal('gold_sell', 10, 2)->nullable();
            $table->float('percent')->nullable();
            $table->decimal('dollar_price', 10, 2)->nullable();
            $table->decimal('gold_with_work', 10, 2)->nullable();
            $table->decimal('gold_in_diamond', 10, 2)->nullable();
            $table->decimal('shoghl_agnaby', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gold_prices');
    }
};