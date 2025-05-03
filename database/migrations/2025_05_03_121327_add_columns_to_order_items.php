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
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('cost', 10, 2)->nullable()->after('order_type');
            $table->decimal('gold_weight', 10, 2)->nullable()->after('cost');
            $table->string('new_barcode')->nullable()->after('gold_weight');
            $table->string('new_diamond_number')->nullable()->after('new_barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('cost');
            $table->dropColumn('gold_weight');
            $table->dropColumn('new_barcode');
            $table->dropColumn('new_diamond_number');
        });
    }
};
