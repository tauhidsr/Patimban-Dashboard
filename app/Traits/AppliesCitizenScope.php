<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait AppliesCitizenScope
{
    /**
     * Apply scope wilayah untuk operator (dusun/rw/rt) pada tabel citizens.
     * - Admin/viewer: bebas
     * - Operator: wajib punya dusun
     *   - kalau $abortIfMissingDusun = true -> abort(403)
     *   - kalau false -> kosongkan hasil (whereRaw 1=0)
     */
    protected function scopeCitizenForOperator(
        Builder $query,
        $user,
        string $prefix = '',
        bool $abortIfMissingDusun = true
    ): Builder {
        if (($user->role ?? 'viewer') !== 'operator') {
            return $query;
        }

        if (empty($user->dusun)) {
            if ($abortIfMissingDusun) {
                abort(403, 'Akun operator belum memiliki scope wilayah (dusun). Hubungi admin.');
            }

            // biar aman tidak bocor data
            return $query->whereRaw('1=0');
        }

        $p = $prefix ? rtrim($prefix, '.') . '.' : '';

        $query->where($p . 'dusun', $user->dusun);

        if (!empty($user->rw)) {
            $query->where($p . 'rw', $user->rw);
        }

        if (!empty($user->rt)) {
            $query->where($p . 'rt', $user->rt);
        }

        return $query;
    }
}
