<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadJobsDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_jobs_data', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->unsignedInteger('upload_job_id');
            $table->string('model');
            $table->text('model_data');

            $table->timestamps();

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
        Schema::table('upload_jobs_data', function (Blueprint $table) {
            $table->dropForeign(['upload_job_id']);
        });
        Schema::drop('upload_jobs_data');
    }
}
