<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // scope wilayah untuk operator/viewer
            $table->string('dusun')->nullable()->after('role');
            $table->string('rw', 3)->nullable()->after('dusun');
            $table->string('rt', 3)->nullable()->after('rw');

            // opsional tapi berguna buat laporan/audit nanti
            $table->string('jabatan', 50)->nullable()->after('rt'); // contoh: Kadus / Ketua RW / Ketua RT / Kades
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dusun', 'rw', 'rt', 'jabatan']);
        });
    }
};
