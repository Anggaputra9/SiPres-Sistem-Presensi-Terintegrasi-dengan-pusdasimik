<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->get('role');

        $users = User::query()
            ->when($role, fn($q) => $q->where('role', $role))
            ->whereIn('role', [User::ROLE_DOSEN, User::ROLE_MAHASISWA])
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users', 'role'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => 'required|string|max:32|unique:users,username',
            'role' => 'required|in:dosen,mahasiswa',
            'nama' => 'required|string|max:100',
            'program_studi' => 'nullable|string|max:100',
            'fakultas' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'password' => 'required|string|min:6',
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        abort_if($user->role === User::ROLE_ADMIN, 403);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->role === User::ROLE_ADMIN, 403);

        $data = $request->validate([
            'username' => 'required|string|max:32|unique:users,username,' . $user->id,
            'role' => 'required|in:dosen,mahasiswa',
            'nama' => 'required|string|max:100',
            'program_studi' => 'nullable|string|max:100',
            'fakultas' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6',
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->role === User::ROLE_ADMIN, 403);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
