<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRowCountFieldToUploadJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('upload_jobs', function (Blueprint $table) {
            $table->unsignedInteger('row_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('upload_jobs', function (Blueprint $table) {
            $table->dropColumn('row_count');
        });
    }
}
