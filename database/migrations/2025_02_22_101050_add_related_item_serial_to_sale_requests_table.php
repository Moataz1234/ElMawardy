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
        Schema::table('sale_requests', function (Blueprint $table) {
            $table->string('related_item_serial')->nullable()->after('item_serial_number');
            $table->index('related_item_serial');
        });
    }

    public function down()
    {
        Schema::table('sale_requests', function (Blueprint $table) {
            $table->dropColumn('related_item_serial');
        });
    }
};
