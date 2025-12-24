<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('population_events', function (Blueprint $table) {
            // status citizen sebelum event ini disetujui
            $table->string('previous_status_dasar', 50)->nullable()->after('catatan_verifikasi');

            // penanda: event ini sudah pernah diterapkan ke citizen atau belum
            $table->timestamp('status_applied_at')->nullable()->after('previous_status_dasar');
            $table->unsignedBigInteger('status_applied_by')->nullable()->after('status_applied_at');
        });
    }

    public function down(): void
    {
        Schema::table('population_events', function (Blueprint $table) {
            $table->dropColumn(['previous_status_dasar', 'status_applied_at', 'status_applied_by']);
        });
    }
};
