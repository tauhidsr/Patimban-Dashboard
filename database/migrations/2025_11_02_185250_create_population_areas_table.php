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
        Schema::create('population_areas', function (Blueprint $table) {
        $table->id();
        $table->string('nama_wilayah');   // contoh: Dusun 1 / RW 03 / RT 01
        $table->unsignedInteger('jumlah_penduduk'); // total penduduk di wilayah itu
        $table->year('tahun')->nullable(); // kalau nanti mau simpan per tahun
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('population_areas');
    }
};
