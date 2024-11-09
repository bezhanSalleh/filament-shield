<?php

namespace BezhanSalleh\FilamentShield\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncShieldTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Filament::hasTenancy()) {
            setPermissionsTeamId(Filament::getTenant());
        }

        return $next($request);
    }
}
