<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIndexOnSynonymsTablesToBeUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams_synonyms', function (Blueprint $table) {
            $table->dropIndex(['synonym']);
            $table->unique(['synonym']);
        });

        Schema::table('venues_synonyms', function (Blueprint $table) {
            $table->dropIndex(['synonym']);
            $table->unique(['synonym']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams_synonyms', function (Blueprint $table) {
            $table->dropUnique(['synonym']);
            $table->index(['synonym']);
        });

        Schema::table('venues_synonyms', function (Blueprint $table) {
            $table->dropUnique(['synonym']);
            $table->index(['synonym']);
        });
    }
}
