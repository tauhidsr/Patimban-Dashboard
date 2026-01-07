<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // kalau belum login, biarkan auth middleware yang handle
        if (!$user) {
            return $next($request);
        }

        // kalau wajib ganti password, batasi route yang boleh diakses
        if (($user->must_change_password ?? false) === true) {

            // route yang tetap boleh diakses saat dipaksa ganti password
            $allowedRouteNames = [
                'profile.edit',
                'profile.update',
                'password.update',
                'logout',
            ];

            $currentRouteName = $request->route()?->getName();

            // kalau route name kosong, biarkan lanjut (hindari edge-case)
            if ($currentRouteName && !in_array($currentRouteName, $allowedRouteNames, true)) {
                return redirect()
                    ->route('profile.edit')
                    ->with('force_password_change', 'Silakan ganti password terlebih dahulu sebelum menggunakan sistem.');
            }
        }

        return $next($request);
    }
}
