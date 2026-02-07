<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOrSeller
{
    public function handle(Request $request, Closure $next)
    {
        if (auth('admin')->check() || auth('seller')->check()) {
            return $next($request);
        }

        return redirect()->route('admin.login.get')
            ->with('error', 'Please log in first.');
    }
}
