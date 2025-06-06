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
        Schema::create('kasr_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasr_sale_id')->constrained()->onDelete('cascade');
            $table->string('kind')->nullable();
            $table->string('metal_purity');
            $table->decimal('weight', 10, 2);
            $table->decimal('net_weight', 10, 2)->nullable();
            $table->string('item_type')->default('customer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasr_items');
    }
}; 