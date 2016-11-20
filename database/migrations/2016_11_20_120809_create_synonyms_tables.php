<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSynonymsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venues_synonyms', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('synonym');
            $table->unsignedInteger('venue_id');

            $table->timestamps();

            $table->index('synonym');
            $table->index('venue_id');

            $table->foreign('venue_id')->references('id')->on('venues');
        });

        Schema::create('teams_synonyms', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('synonym');
            $table->unsignedInteger('team_id');

            $table->timestamps();

            $table->index('synonym');
            $table->index('team_id');

            $table->foreign('team_id')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('venues_synonyms', function (Blueprint $table) {
            $table->dropForeign(['venue_id']);
        });
        Schema::drop('venues_synonyms');

        Schema::table('teams_synonyms', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
        });
        Schema::drop('teams_synonyms');
    }
}
