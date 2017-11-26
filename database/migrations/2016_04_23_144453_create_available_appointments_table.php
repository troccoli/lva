<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvailableAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('available_appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fixture_id');
            $table->unsignedInteger('role_id');
            $table->timestamps();

            $table->unique(['fixture_id', 'role_id']);

            $table->foreign('fixture_id')->references('id')->on('fixtures');
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('available_appointments');
    }
}
