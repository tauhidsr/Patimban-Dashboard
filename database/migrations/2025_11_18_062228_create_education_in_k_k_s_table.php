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
        Schema::create('education_in_k_k_s', function (Blueprint $table) {
            $table->id();

            // contoh: "TIDAK/BELUM SEKOLAH", "BELUM TAMAT SD/SEDERAJAT", dst
            $table->string('kategori');

            // jumlah jiwa per kategori pendidikan dalam KK
            $table->unsignedInteger('laki_laki')->nullable();
            $table->unsignedInteger('perempuan')->nullable();
            $table->unsignedInteger('total')->nullable(); // auto dari L+P

            // tahun data, saat ini 2025
            $table->year('tahun')->nullable()->default(2025);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_in_k_k_s');
    }
};
