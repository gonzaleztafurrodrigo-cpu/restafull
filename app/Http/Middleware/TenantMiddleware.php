<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        $centralDomains = config('tenancy.central_domains');

        if (in_array($host, $centralDomains)) {
            return $next($request);
        }

        // Buscar tenant por dominio completo o subdominio
        $tenant = Tenant::whereHas('domains', function ($query) use ($host) {
            $query->where('domain', $host);
        })->first();

        // Si no encontró por dominio completo, intentar por subdominio
        // Ej: pizzahoot.restafull.com → buscar pizzahoot.restafull.com
        if (!$tenant) {
            // Extraer subdominio base (sin el dominio central)
            $centralDomain = collect($centralDomains)
                ->filter(fn($d) => str_ends_with($host, '.' . $d))
                ->first();

            if ($centralDomain) {
                $subdomain = str_replace('.' . $centralDomain, '', $host);
                $tenant = Tenant::whereHas('domains', function ($query) use ($subdomain, $centralDomain) {
                    $query->where('domain', $subdomain . '.' . $centralDomain)
                          ->orWhere('domain', $subdomain);
                })->first();
            }
        }

        if (!$tenant) {
            abort(404, 'Restaurante no encontrado.');
        }

        if (!$tenant->is_active) {
            abort(403, 'Este restaurante está inactivo.');
        }

        tenancy()->initialize($tenant);

        \Illuminate\Support\Facades\Auth::logout();

        $dbName = 'tenant_' . $tenant->id;
        config(['database.connections.tenant.database' => $dbName]);
        \Illuminate\Support\Facades\DB::purge('tenant');
        \Illuminate\Support\Facades\DB::reconnect('tenant');
        \Illuminate\Support\Facades\DB::setDefaultConnection('tenant');

        return $next($request);
    }
}
