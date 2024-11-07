<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('models', function (Blueprint $table) {
            $table->foreign('category')
                  ->references('category')
                  ->on('category_prices')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('models', function (Blueprint $table) {
            $table->dropForeign(['category']);
        });
    }
};
