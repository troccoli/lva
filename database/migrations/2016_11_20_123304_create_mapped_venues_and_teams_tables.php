<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMappedVenuesAndTeamsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapped_venues', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('upload_job_id')->nullable();
            $table->string('venue')->default('')->index();
            $table->unsignedInteger('venue_id')->nullable();

            $table->timestamps();

            //$table->foreign('upload_job_id')->references('id')->on('upload_jobs');
            //$table->foreign('venue_id')->references('id')->on('venues');
        });

        Schema::create('mapped_teams', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('upload_job_id')->nullable();
            $table->string('team')->default('')->index();
            $table->unsignedInteger('team_id')->nullable();

            $table->timestamps();

            //$table->index(['team']);
            //$table->foreign('upload_job_id')->references('id')->on('upload_jobs');
            //$table->foreign('team_id')->references('id')->on('teams');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mapped_venues', function (Blueprint $table) {
            $table->dropForeign(['upload_job_id']);
            $table->dropForeign(['venue_id']);
        });
        Schema::drop('mapped_venues');

        Schema::table('mapped_teams', function (Blueprint $table) {
            $table->dropForeign(['upload_job_id']);
            $table->dropForeign(['team_id']);
        });
        Schema::drop('mapped_teams');
    }
}
