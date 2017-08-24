<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
            $table->dropIndex(['venue']);
            $table->renameColumn('venue', 'mapped_venue');
            $table->index(['mapped_venue']);
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
            $table->dropIndex(['mapped_venue']);
            $table->renameColumn('mapped_venue', 'venue');
            $table->index(['venue']);
        });
    }
}
