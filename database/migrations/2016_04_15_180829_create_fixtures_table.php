<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFixturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixtures', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('division_id');
            $table->unsignedInteger('match_number');
            $table->date('match_date');
            $table->time('warm_up_time');
            $table->time('start_time');
            $table->unsignedInteger('home_team_id');
            $table->unsignedInteger('away_team_id');
            $table->unsignedInteger('venue_id');

            $table->timestamps();

            $table->foreign('division_id')->references('id')->on('divisions');
            $table->foreign('home_team_id')->references('id')->on('teams');
            $table->foreign('away_team_id')->references('id')->on('teams');
            $table->foreign('venue_id')->references('id')->on('venues');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropForeign(['home_team_id']);
            $table->dropForeign(['away_team_id']);
            $table->dropForeign(['venue_id']);
        });
        Schema::drop('fixtures');
    }
}
