<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    public function handle($request, Closure $next): Response
    {
        if(auth()->user()->role != 'superadmin'){
            abort(403);
        }
        return $next($request);
    }
}
