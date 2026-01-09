<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('role', 'like', "%{$q}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|lowercase|email|max:255|unique:users,email',

                // Opsi A: admin hanya boleh buat operator/viewer
                'role'     => 'required|in:operator,viewer',

                'password' => 'nullable|string|min:8',

                // scope wilayah (nullable, tapi nanti kita cek kondisional)
                'dusun'    => 'nullable|string|max:100',
                'rw'       => 'nullable|string|max:3',
                'rt'       => 'nullable|string|max:3',
                'jabatan'  => 'nullable|string|max:50',
            ],
            [
                'role.in' => 'Role harus operator / viewer.',
                'email.unique' => 'Email sudah terdaftar.',
            ]
        );

        // =========================
        // Validasi kondisional scope
        // =========================
        $role = $validated['role'];

        // viewer (kades) -> tidak perlu scope wilayah (null semua)
        if ($role === 'viewer') {
            $validated['dusun'] = null;
            $validated['rw'] = null;
            $validated['rt'] = null;
        }

        // operator -> minimal harus punya dusun
        if ($role === 'operator') {
            if (empty($validated['dusun'])) {
                return back()
                    ->withErrors(['dusun' => 'Untuk operator, Dusun wajib diisi.'])
                    ->withInput();
            }

            // kalau isi RT, RW wajib ada
            if (!empty($validated['rt']) && empty($validated['rw'])) {
                return back()
                    ->withErrors(['rw' => 'Jika RT diisi, RW wajib diisi.'])
                    ->withInput();
            }
        }

        $plainPassword = $validated['password'] ?: Str::random(12);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => Hash::make($plainPassword),

            'dusun'    => $validated['dusun'] ?? null,
            'rw'       => $validated['rw'] ?? null,
            'rt'       => $validated['rt'] ?? null,
            'jabatan'  => $validated['jabatan'] ?? null,

            // wajib ganti password setelah login pertama
            'must_change_password' => true,
            'password_changed_at'  => null,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Akun berhasil dibuat: {$user->email}")
            ->with('temp_password', $plainPassword);
    }

    public function resetPassword(User $user)
    {
        // ✅ safety: jangan reset password akun admin
        if (($user->role ?? null) === 'admin') {
            return back()->with('error', 'Password akun admin tidak boleh di-reset dari menu ini.');
        }

        // ✅ safety: jangan reset password diri sendiri dari menu ini
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Tidak bisa reset password akun sendiri dari menu ini.');
        }

        // reset password random (ditampilkan 1x via session)
        $plainPassword = Str::random(12);

        $user->password = Hash::make($plainPassword);

        // ✅ setelah reset, paksa user ganti password lagi
        $user->must_change_password = true;
        $user->password_changed_at  = null;

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Password berhasil di-reset untuk: {$user->email}")
            ->with('temp_password', $plainPassword);
    }

    public function destroy(User $user)
    {
        // safety: jangan bisa hapus akun admin (biar desa ga ke-lock)
        if (($user->role ?? null) === 'admin') {
            return back()->with('error', 'Akun admin tidak boleh dihapus.');
        }

        // safety: jangan hapus diri sendiri
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun berhasil dihapus.');
    }
}
