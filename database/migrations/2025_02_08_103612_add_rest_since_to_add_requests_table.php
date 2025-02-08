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
        Schema::table('add_requests', function (Blueprint $table) {
            $table->date('rest_since')->nullable()->after('talab');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('add_requests', function (Blueprint $table) {
            $table->dropColumn('rest_since');
        });
    }
};
