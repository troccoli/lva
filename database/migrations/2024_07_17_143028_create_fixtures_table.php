<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixtures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('match_number');
            $table->foreignUuid('division_id')
                ->constrained('divisions');
            $table->foreignUuid('home_team_id')
                ->constrained('teams');
            $table->foreignUuid('away_team_id')
                ->constrained('teams');
            $table->date('match_date');
            $table->time('start_time');
            $table->foreignUuid('venue_id')
                ->constrained('venues');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['division_id', 'match_number']);
            $table->index(['match_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixtures');
    }
};
