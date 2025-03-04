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
        Schema::table('gold_items', function (Blueprint $table) {
            $table->string('source')->nullable()->after('rest_since');
        });

        Schema::table('gold_items_sold', function (Blueprint $table) {
            $table->string('source')->nullable()->after('stones');
        });
        Schema::table('add_requests', function (Blueprint $table) {
            $table->string('source')->nullable()->after('rest_since');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gold_items', function (Blueprint $table) {
            $table->dropColumn('source');
        });

        Schema::table('gold_items_sold', function (Blueprint $table) {
            $table->dropColumn('source');
        });
        Schema::table('add_requests', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};