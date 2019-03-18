<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEventsMakeNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
	    Schema::table('events', function (Blueprint $table) {
			$table->float('delay')->nullable()->change();
			$table->float('time')->nullable()->change();
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->float('delay')->nullable(false)->change();
	        $table->float('time')->nullable(false)->change();
        });
    }
}
