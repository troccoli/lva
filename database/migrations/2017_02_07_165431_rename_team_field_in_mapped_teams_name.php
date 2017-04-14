<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->dropIndex(['team']);
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
            $table->dropIndex(['mapped_team']);
            $table->renameColumn('mapped_team', 'team');
            $table->index(['team']);
        });
    }
}
