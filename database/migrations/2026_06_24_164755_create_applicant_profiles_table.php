<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('resume_path')->nullable();
            $table->string('resume_original_name')->nullable();
            $table->string('resume_file_type')->nullable();
            $table->string('resume_mime_type')->nullable();
            $table->unsignedBigInteger('resume_file_size')->nullable();
            $table->string('resume_disk')->default('public');

            $table->jsonb('skills')->nullable();
            $table->jsonb('experience')->nullable();
            $table->jsonb('education')->nullable();
            $table->jsonb('portfolio_urls')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_profiles');
    }
};
