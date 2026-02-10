<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!$request->user()) {
            return redirect('login');
        }

        if (!$request->user()->hasRole($role)) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini');
        }

        return $next($request);
    }
}
