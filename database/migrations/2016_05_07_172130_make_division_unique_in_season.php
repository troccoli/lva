<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeDivisionUniqueInSeason extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->unique(['season_id', 'division'], 'unique_division');
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
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropForeign(['season_id']);
            $table->dropIndex('unique_division');
            $table->foreign('season_id')->references('id')->on('seasons');
        });
    }
}
