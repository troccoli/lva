<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveVenueIdFromNewVenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_venues', function (Blueprint $table) {
            $table->dropColumn('venue_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_venues', function (Blueprint $table) {
            $table->unsignedInteger('venue_id')->nullable();
        });
    }
}
