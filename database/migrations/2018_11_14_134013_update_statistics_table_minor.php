<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStatisticsTableMinor extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('statistics', function (Blueprint $table) {
			$table->dropForeign('statistics_event_id_foreign');
			$table->dropColumn('event_id');
			$table->integer('popup_id')->unsigned();
			$table->foreign('popup_id')->references('id')->on('popups')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('statistics', function (Blueprint $table) {
			$table->dropForeign('statistics_popup_id_foreign');
			$table->dropColumn('popup_id');
			$table->integer('event_id')->unsigned();
			$table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
		});
	}
}
