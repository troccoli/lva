<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('season_id')
                ->constrained('seasons');
            $table->string('name');
            $table->timestamps();

            $table->unique(['season_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
