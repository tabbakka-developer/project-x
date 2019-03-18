<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('statistics', function (Blueprint $table) {
            $table->dropColumn('action');
            $table->boolean('viewed');
            $table->boolean('closed');
            $table->boolean('clicked');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('statistics', function (Blueprint $table) {
            $table->dropColumn('viewed');
            $table->dropColumn('closed');
            $table->dropColumn('clicked');
        	$table->integer('action');
        });
    }
}
