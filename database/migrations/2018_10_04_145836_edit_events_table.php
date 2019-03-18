<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
	        $table->dropColumn('title');
	        $table->dropColumn('image');
	        $table->dropColumn('message');
	        $table->dropColumn('url');
	        $table->string('name');
	        $table->text('url_from')->nullable();
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
	        $table->dropColumn('name');
	        $table->dropColumn('url_from');
	        $table->string('title');
	        $table->string('image');
	        $table->text('message');
	        $table->text('url');
        });
    }
}
