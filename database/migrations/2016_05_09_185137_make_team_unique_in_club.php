<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeTeamUniqueInClub extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->unique(['club_id', 'team'], 'unique_team_in_club');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Adding the unique index deleted the index on season_id necessary for the foreign key
        // MySQL would also complain if we try to just drop the unique index saying that it's
        // necessary for the foreign key (sigh). This is why we drop the foreign key and add it again
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->dropIndex('unique_team_in_club');
            $table->foreign('club_id')->references('id')->on('clubs');
        });
    }
}
