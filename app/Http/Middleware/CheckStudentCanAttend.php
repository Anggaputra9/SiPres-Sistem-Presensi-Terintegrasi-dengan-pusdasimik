<?php

namespace App\Http\Middleware;

use App\Services\PusatDataClient;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckStudentCanAttend
{
    public function __construct(protected PusatDataClient $client)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Hanya cek untuk mahasiswa
        if (!$user || !$user->isMahasiswa()) {
            return $next($request);
        }

        // Cache permissions selama 5 menit untuk mengurangi API calls
        $cacheKey = "student_permissions_{$user->username}";
        
        $permissions = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user) {
            return $this->client->checkMahasiswaPermissions($user->username);
        });

        // Jika gagal get permissions atau tidak bisa presensi
        if (!$permissions || !($permissions['permissions']['can_attend'] ?? false)) {
            $status = $permissions['status_label'] ?? 'tidak aktif';
            $message = $this->getBlockedMessage($permissions['status'] ?? null);
            
            return redirect()
                ->route('mahasiswa.dashboard')
                ->with('error', $message);
        }

        return $next($request);
    }

    private function getBlockedMessage(?string $status): string
    {
        return match($status) {
            'cuti' => 'Anda sedang dalam status CUTI. Tidak dapat melakukan presensi.',
            'lulus' => 'Anda sudah LULUS. Tidak dapat melakukan presensi.',
            'do' => 'Status akun Anda DROP OUT. Tidak dapat melakukan presensi.',
            default => 'Status akun Anda tidak memungkinkan untuk melakukan presensi. Silakan hubungi admin.',
        };
    }
}
