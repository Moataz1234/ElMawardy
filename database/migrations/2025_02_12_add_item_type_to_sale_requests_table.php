<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sale_requests', function (Blueprint $table) {
            $table->string('related_item_serial')->nullable()->after('item_serial_number');
            $table->index('related_item_serial');
            $table->string('item_type')->nullable()->after('related_item_serial');
            $table->string('weight')->nullable()->after('item_type');
            $table->string('purity')->nullable()->after('weight');
            $table->string('kind')->nullable()->after('purity');
        });
    }

    public function down()
    {
        Schema::table('sale_requests', function (Blueprint $table) {
            $table->dropColumn('item_type');
        });
    }
}; 