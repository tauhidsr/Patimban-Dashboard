// ⚠️ MIGRATION INI TIDAK DIGUNAKAN LAGI
// Digantikan oleh 2025_11_13_xxxx_ensure_coords_columns_on_population_areas
// Dibiarkan untuk menjaga urutan batch migration lama


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('population_areas', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('tahun');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('population_areas', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
