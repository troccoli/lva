<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTeamFieldInMappedTeamsName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mapped_teams', function (Blueprint $table) {
            // No need to drop and re-create the index
            $table->renameColumn('team', 'mapped_team');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mapped_teams', function (Blueprint $table) {
            // No need to drop and re-create the index
            $table->renameColumn('mapped_team', 'team');
        });
    }
}
