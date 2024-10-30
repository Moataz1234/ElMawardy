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
        Schema::create('outers', function (Blueprint $table) {
            $table->id();
            $table->string('gold_serial_number')->nullable();
            $table->foreign('gold_serial_number')->references('serial_number')->on('gold_items')->onDelete('set null');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone_number', 11)->nullable();
            $table->string(column: 'reason')->nullable();
            $table->boolean('is_returned')->default(false); // False means not returned yet
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outers', function (Blueprint $table) {
            $table->dropForeign(['gold_serial_number']);
            $table->dropColumn('gold_serial_number');
        });
    }
};
