<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/2026_06_30_000000_create_applications_table.php
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('cv_path')->nullable(); // Path penyimpanan CV
            $table->string('resume_original_name')->nullable(); // Nama file asli CV
            $table->text('cover_letter');
            $table->text('parsed_data')->nullable();
            $table->jsonb('skills')->nullable();
            $table->jsonb('experience')->nullable();
            $table->jsonb('education')->nullable();
            $table->jsonb('portfolio_urls')->nullable();
            $table->foreignId('applicant_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_position_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('applied');
            $table->jsonb('custom_answers')->nullable();
            $table->text('hrd_notes')->nullable();
            $table->timestamps();
        });

    }
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
