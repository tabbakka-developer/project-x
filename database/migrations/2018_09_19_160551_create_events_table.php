<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('image');
            $table->text('message');
            $table->text('url');
            $table->integer('desktop_placement');
            $table->integer('mobile_placement');
            $table->float('delay');
            $table->boolean('repeat');
            $table->float('time');
            $table->boolean('close');
            $table->boolean('new_tab');
            $table->boolean('round');
            $table->string('title_color');
            $table->string('background_color');
            $table->string('message_color');
	        $table->integer('user_id')->unsigned();
	        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

//        Schema::table('events', function (Blueprint $table) {
//	        $table->foreign('user_id')
//		        ->references('id')->on('users')
//		        ->onDelete('cascade');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
