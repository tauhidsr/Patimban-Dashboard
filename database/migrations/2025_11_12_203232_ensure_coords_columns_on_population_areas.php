<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // tambah kolom yang belum ada
        if (!Schema::hasColumn('population_areas', 'latitude')) {
            Schema::table('population_areas', function (Blueprint $table) {
                $table->decimal('latitude', 10, 7)->nullable()->after('tahun');
            });
        }

        if (!Schema::hasColumn('population_areas', 'longitude')) {
            Schema::table('population_areas', function (Blueprint $table) {
                // letakkan setelah latitude; kalau latitude belum ada, tetap akan ditaruh sesuai engine
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            });
        }
    }

    public function down(): void
    {
        // aman: drop hanya kalau ada
        if (Schema::hasColumn('population_areas', 'longitude')) {
            Schema::table('population_areas', function (Blueprint $table) {
                $table->dropColumn('longitude');
            });
        }

        if (Schema::hasColumn('population_areas', 'latitude')) {
            Schema::table('population_areas', function (Blueprint $table) {
                $table->dropColumn('latitude');
            });
        }
    }
};
