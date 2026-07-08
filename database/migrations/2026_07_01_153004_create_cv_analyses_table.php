<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cv_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->jsonb('skill_match')->nullable();
            $table->jsonb('experience_match')->nullable();
            $table->jsonb('education_match')->nullable();
            $table->string('recommendation')->nullable();
            $table->text('reasoning')->nullable();
            $table->jsonb('raw_ai_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_analyses');
    }
};
