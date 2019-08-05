<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVenueToTeams extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->uuid('venue_id')->nullable();

            $table->foreign('venue_id')->references('id')->on('venues');
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('teams', function (Blueprint $table) {
                $table->dropForeign(['venue_id']);
            });
        }
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('venue_id');
        });
    }
}
