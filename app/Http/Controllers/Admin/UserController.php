<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:super_admin,editor'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $tempPassword = Str::password(14);

        User::create([
            ...$data,
            'is_active' => $request->boolean('is_active', true),
            'password' => Hash::make($tempPassword),
        ]);

        return redirect()->route('admin.users.index')
            ->with('status', "Akun dibuat. Kata sandi sementara: {$tempPassword} (sampaikan ke pengguna, minta segera diganti lewat Pengaturan Akun).");
    }

    public function edit(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:super_admin,editor'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        abort_if(
            $user->id === $request->user()->id && $data['role'] !== 'super_admin',
            403,
            'Tidak bisa menurunkan role akun sendiri.'
        );

        $user->update([...$data, 'is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.users.index')->with('status', 'Akun berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 403, 'Tidak bisa menghapus akun sendiri.');

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'Akun berhasil dihapus.');
    }
}
