<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MakeDivisionAndTeamsUniqueInFixtures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->unique(['division_id', 'home_team_id', 'away_team_id'], 'unique_division_and_teams');
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
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropIndex('unique_division_and_teams');
            $table->foreign('division_id')->references('id')->on('divisions');
        });
    }
}
