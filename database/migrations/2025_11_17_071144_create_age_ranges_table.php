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
        Schema::create('age_ranges', function (Blueprint $table) {
            $table->id();
            
            // cnth isi "7 s/d 16 tahun", "di atas 18 tahun", "belum mengisi"
            $table->string('kategori');

            // jumlah jiwa per kategori umur
            $table->unsignedInteger('laki_laki')->nullable();
            $table->unsignedInteger('perempuan')->nullable();
            $table->unsignedInteger('total')->nullable();

            // tahun data, default 2025
            $table->year('tahun')->nullable()->default(2025);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('age_ranges');
    }
};
