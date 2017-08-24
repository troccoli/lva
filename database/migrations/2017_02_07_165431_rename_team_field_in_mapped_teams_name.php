<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
            $table->dropIndex('mapped_teams_team_index');
            $table->renameColumn('team', 'mapped_team');
            $table->index(['mapped_team']);
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
            $table->dropIndex('mapped_teams_mapped_team_index');
            $table->renameColumn('mapped_team', 'team');
            $table->index(['team']);
        });
    }
}
