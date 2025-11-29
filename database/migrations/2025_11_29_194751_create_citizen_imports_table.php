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
        Schema::create('citizen_imports', function (Blueprint $table) {
            $table->id();

            // relasi opsional ke master citizens (nanti saat sudah dipetakan)
            $table->foreignId('citizen_id')
                ->nullable()
                ->constrained('citizens')
                ->nullOnDelete();

            // informasi sumber data
            $table->string('source_file')->nullable();   // nama file excel
            $table->unsignedInteger('row_index')->nullable(); // baris ke-berapa di Excel

            // kolom utama yang kemungkinan kita pakai
            $table->string('nik', 16)->nullable();
            $table->string('no_kk', 16)->nullable();
            $table->string('nama')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();

            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();

            $table->string('agama')->nullable();
            $table->string('pendidikan_dalam_kk')->nullable();
            $table->string('pendidikan_sedang_ditempuh')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('status_perkawinan')->nullable();
            $table->string('hubungan_dalam_keluarga')->nullable();
            $table->string('kewarganegaraan')->nullable();

            $table->string('dusun')->nullable();
            $table->string('rw')->nullable();
            $table->string('rt')->nullable();

            $table->text('alamat')->nullable();
            $table->text('alamat_sekarang')->nullable();
            $table->string('status_dasar')->nullable();
            $table->string('suku')->nullable();

            // koordinat (kalau di excel ada lat/lng)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // raw row untuk menyimpan data mentah (kolom lain yang tidak kita pakai tetap bisa disimpan)
            $table->json('raw_row')->nullable();

            // status proses impor
            $table->enum('import_status', ['pending', 'matched', 'imported', 'skipped', 'error'])
                ->default('pending');
            $table->text('error_message')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citizen_imports');
    }
};
