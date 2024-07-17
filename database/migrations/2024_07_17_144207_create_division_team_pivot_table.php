<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('division_team', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('division_id')
                ->constrained('divisions');
            $table->foreignUuid('team_id')
                ->constrained('teams');
            $table->timestamps();

            $table->unique(['division_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('division_team');
    }
};
