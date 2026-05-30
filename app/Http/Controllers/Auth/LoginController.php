<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PusatDataClient;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(protected PusatDataClient $pusatData) {}

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => 'required|string|max:32',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (! $user) {
            $user = $this->provisionFromPusatData($credentials['username']);
        }

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended($user->isDosen() ? route('dosen.dashboard') : route('mahasiswa.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function provisionFromPusatData(string $username): ?User
    {
        try {
            $data = $this->pusatData->findMahasiswa($username);
            $role = User::ROLE_MAHASISWA;

            if (! $data) {
                $data = $this->pusatData->findDosen($username);
                $role = User::ROLE_DOSEN;
            }
        } catch (RequestException) {
            throw ValidationException::withMessages([
                'username' => 'Tidak bisa menghubungi Pusat Data. Hubungi admin.',
            ]);
        }

        if (! $data) {
            return null;
        }

        $payload = [
            'username' => $username,
            'role' => $role,
            'nama' => $data['nama'] ?? $username,
            'password' => Hash::make($username),
            'last_synced_at' => now(),
        ];

        if ($role === User::ROLE_MAHASISWA) {
            $unit = $data['unit'] ?? [];
            $payload['program_studi'] = $unit['nama'] ?? ($data['program_studi'] ?? null);
            $payload['fakultas'] = $unit['parent']['nama'] ?? ($data['fakultas'] ?? null);
        } else {
            $payload['jabatan'] = $data['jabatan'] ?? 'Dosen';
        }

        return User::create($payload);
    }
}
