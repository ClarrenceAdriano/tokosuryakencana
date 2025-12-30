<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // LOGIKA PEMERIKSAAN (GATE CHECK):
        // Syarat masuk ada dua:
        // 1. Auth::check() -> User HARUS sudah login (tidak boleh tamu/guest).
        // 2. Auth::user()->role === 'admin' -> Kolom 'role' di database harus 'admin'.
        if (Auth::check() && Auth::user()->role === 'admin') {
            // $next($request) artinya: "Silakan lewat, lanjutkan ke Controller."
            return $next($request);
        }
        return redirect('/');
    }
}