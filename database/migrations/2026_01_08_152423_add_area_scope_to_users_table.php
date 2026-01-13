<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'dusun')) {
                $table->string('dusun')->nullable()->after('role');
            }

            if (!Schema::hasColumn('users', 'rw')) {
                $table->string('rw', 3)->nullable()->after('dusun');
            }

            if (!Schema::hasColumn('users', 'rt')) {
                $table->string('rt', 3)->nullable()->after('rw');
            }

            if (!Schema::hasColumn('users', 'jabatan')) {
                $table->string('jabatan', 50)->nullable()->after('rt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $drops = [];

            if (Schema::hasColumn('users', 'dusun')) $drops[] = 'dusun';
            if (Schema::hasColumn('users', 'rw')) $drops[] = 'rw';
            if (Schema::hasColumn('users', 'rt')) $drops[] = 'rt';
            if (Schema::hasColumn('users', 'jabatan')) $drops[] = 'jabatan';

            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
