<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Role: super_admin, hrd, candidate
            $table->string('role')->default('candidate')->after('email');

            // Status: active, suspended, inactive
            $table->enum('status', ['active', 'suspended', 'inactive'])
                  ->default('active')
                  ->after('role');

            // Alasan suspend (optional)
            $table->text('suspend_reason')->nullable()->after('status');

            // Tanggal suspend
            $table->timestamp('suspended_at')->nullable()->after('suspend_reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'suspend_reason', 'suspended_at']);
        });
    }
};
