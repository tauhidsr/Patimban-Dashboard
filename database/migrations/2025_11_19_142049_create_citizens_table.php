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
        Schema::create('citizens', function (Blueprint $table) {
            $table->id();

            // identitas dasar
            $table->string('nik', 20)->unique();
            $table->string('no_kk', 20)->index();   // 🔹 ditambah index biar pencarian cepat
            $table->string('nama', 150);

            // jenis kelamin L/P
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();

            // tanggal lahir (opsional)
            $table->date('tanggal_lahir')->nullable();

            // lokasi domisili -> rencana dikaitkan dengan tabel dusun/rw/rt
            $table->unsignedBigInteger('dusun_id')->nullable();
            $table->unsignedBigInteger('rw_id')->nullable();
            $table->unsignedBigInteger('rt_id')->nullable();

            // status domisili & status aktif
            $table->enum('status_domisili', ['tetap', 'sementara'])->default('tetap');
            $table->enum('status_aktif', ['aktif', 'pindah', 'meninggal', 'hilang'])->default('aktif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citizens');
    }
};
