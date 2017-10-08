<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameVenueFieldInMappedVenuesName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mapped_venues', function (Blueprint $table) {
            // No need to drop and re-create the index
            $table->renameColumn('venue', 'mapped_venue');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mapped_venues', function (Blueprint $table) {
            // No need to drop and re-create the index
            $table->renameColumn('mapped_venue', 'venue');
        });
    }
}
