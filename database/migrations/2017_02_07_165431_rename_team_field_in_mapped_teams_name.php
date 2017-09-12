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
            //if (! DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
            $table->dropIndex(['team']);
            //}
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
            //if (! DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
            $table->dropIndex(['mapped_team']);
            //}
            $table->renameColumn('mapped_team', 'team');
            $table->index(['team']);
        });
    }
}
