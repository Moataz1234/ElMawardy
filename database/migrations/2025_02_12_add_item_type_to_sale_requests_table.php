<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sale_requests', function (Blueprint $table) {
            $table->string('item_type')->nullable()->after('item_serial_number');
        });
    }

    public function down()
    {
        Schema::table('sale_requests', function (Blueprint $table) {
            $table->dropColumn('item_type');
        });
    }
}; 