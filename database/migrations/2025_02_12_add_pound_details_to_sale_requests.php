<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sale_requests', function (Blueprint $table) {
            $table->string('weight')->nullable()->after('item_type');
            $table->string('purity')->nullable()->after('weight');
            $table->string('kind')->nullable()->after('purity');
        });
    }

    public function down()
    {
        Schema::table('sale_requests', function (Blueprint $table) {
            $table->dropColumn(['weight', 'purity', 'kind']);
        });
    }
}; 