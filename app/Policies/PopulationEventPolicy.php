<?php

namespace App\Policies;

use App\Models\PopulationEvent;
use App\Models\User;
use App\Models\Citizen;

class PopulationEventPolicy
{
    /**
     * Semua user login boleh lihat list.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Detail event:
     * - admin/viewer: boleh
     * - operator: hanya jika event citizen masih dalam scope wilayahnya (dusun/rw/rt)
     */
    public function view(User $user, PopulationEvent $event): bool
    {
        $role = $user->role ?? 'viewer';

        if ($role !== 'operator') {
            return true;
        }

        if (empty($user->dusun)) {
            return false;
        }

        $citizenQuery = Citizen::query();

        // Untuk event lahir, prioritas cek ibu
        if ($event->jenis_peristiwa === 'lahir') {
            if (!empty($event->ibu_citizen_id)) {
                $citizenQuery->where('id', $event->ibu_citizen_id);
            } elseif (!empty($event->nik_ibu)) {
                $citizenQuery->where('nik', $event->nik_ibu);
            } else {
                return false;
            }
        } else {
            if (!empty($event->citizen_id)) {
                $citizenQuery->where('id', $event->citizen_id);
            } elseif (!empty($event->nik)) {
                $citizenQuery->where('nik', $event->nik);
            } else {
                return false;
            }
        }

        $citizenQuery->where('dusun', $user->dusun);

        if (!empty($user->rw)) {
            $citizenQuery->where('rw', $user->rw);
        }

        if (!empty($user->rt)) {
            $citizenQuery->where('rt', $user->rt);
        }

        return $citizenQuery->exists();
    }

    /**
     * Create (form pilih jenis, form meninggal, store, dll)
     */
    public function create(User $user): bool
    {
        $role = $user->role ?? 'viewer';
        return in_array($role, ['admin', 'operator'], true);
    }

    /**
     * Verify admin only.
     */
    public function verify(User $user, PopulationEvent $event): bool
    {
        $role = $user->role ?? 'viewer';
        return $role === 'admin';
    }
}
