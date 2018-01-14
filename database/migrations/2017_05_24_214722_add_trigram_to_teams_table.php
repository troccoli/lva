<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrigramToTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();

        try {
            Schema::table('teams', function (Blueprint $table) {
                $table->char('trigram', 3)->after('team')->default('###');
            });

            $teamIds = DB::table('teams')->pluck('id');

            foreach ($teamIds as $teamId) {
                DB::table('teams')->where('id', $teamId)->update(['trigram' => str_pad($teamId, 3, '0', STR_PAD_LEFT)]);
            }

            Schema::table('teams', function (Blueprint $table) {
                $table->unique('trigram');
            });

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('trigram');
        });
    }
}
