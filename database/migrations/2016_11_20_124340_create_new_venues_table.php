<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewVenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_venues', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->unsignedInteger('upload_job_id');
            $table->string('venue');
            $table->unsignedInteger('venue_id')->nullable();

            $table->timestamps();

            $table->index('venue');
            $table->foreign('upload_job_id')->references('id')->on('upload_jobs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_venues', function (Blueprint $table) {
            $table->dropForeign(['upload_job_id']);
        });
        Schema::drop('new_venues');
    }
}