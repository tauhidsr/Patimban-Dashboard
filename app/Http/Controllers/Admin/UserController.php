<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('role', 'like', "%{$q}%");
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
                'role'     => 'required|in:admin,operator,viewer',
                'password' => 'nullable|string|min:8',
            ],
            [
                'role.in' => 'Role harus admin / operator / viewer.',
                'email.unique' => 'Email sudah terdaftar.',
            ]
        );

        // kalau admin tidak isi password, kita generate password sementara
        $plainPassword = $validated['password'] ?: Str::random(12);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => Hash::make($plainPassword),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Akun berhasil dibuat: {$user->email}")
            ->with('temp_password', $plainPassword);
    }

    public function resetPassword(User $user)
    {
        // reset password random (ditampilkan 1x via session)
        $plainPassword = Str::random(12);

        $user->password = Hash::make($plainPassword);
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Password berhasil di-reset untuk: {$user->email}")
            ->with('temp_password', $plainPassword);
    }

    public function destroy(User $user)
    {
        // safety: jangan bisa hapus akun admin (biar desa ga ke-lock)
        if ($user->role === 'admin') {
            return back()->with('error', 'Akun admin tidak boleh dihapus.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun berhasil dihapus.');
    }
}
