<?php

namespace App\Http\Middleware;

use App\Models\User; // ✅ Import User model
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        // ✅ Type hint User agar Intelephense mengenali method-nya
        /** @var User|null $user */
        $user = Auth::user();

        if ($user && $user->isSuspended()) {
            $reason = $user->suspend_reason ?? 'Tidak ada alasan';
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors([
                    'email' => "Akun Anda telah di-suspend. Alasan: {$reason}",
                ]);
        }

        if ($user && !$user->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Akun Anda tidak aktif. Hubungi administrator.',
                ]);
        }

        return $next($request);
    }
}
