<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('population_events', function (Blueprint $table) {
            $table->unsignedBigInteger('verified_by')->nullable()->after('created_by');
            $table->timestamp('verified_at')->nullable()->after('verified_by');

            // optional: FK biar rapi (kalau tabel users ada)
            // $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('population_events', function (Blueprint $table) {
            // optional: drop FK kalau kamu aktifin FK di atas
            // $table->dropForeign(['verified_by']);

            $table->dropColumn(['verified_by', 'verified_at']);
        });
    }
};
