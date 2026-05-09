<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('customer')) {
            return redirect()->route('tenant.client.login');
        }

        return $next($request);
    }
}
