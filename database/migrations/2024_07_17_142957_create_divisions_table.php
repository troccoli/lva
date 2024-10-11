<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('competition_id')
                ->constrained('competitions')
                ->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('display_order');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['competition_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
