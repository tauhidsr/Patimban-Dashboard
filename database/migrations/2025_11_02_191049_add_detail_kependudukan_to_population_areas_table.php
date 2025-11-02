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
        Schema::table('population_areas', function (Blueprint $table) {
            $table->unsignedInteger('kk')->nullable()->after('nama_wilayah');
            $table->unsignedInteger('laki_laki')->nullable()->after('jumlah_penduduk');
            $table->unsignedInteger('perempuan')->nullable()->after('laki_laki');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('population_areas', function (Blueprint $table) {
            $table->dropColumn(['kk', 'laki_laki', 'perempuan']);
        });
    }
};
