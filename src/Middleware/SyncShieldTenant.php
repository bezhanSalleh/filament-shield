<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class SyncShieldTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Filament::hasTenancy() && $tenant = Filament::getTenant()) {

            setPermissionsTeamId($tenant->getKey());

            if (auth()->hasUser()) {
                auth()
                    ->user()
                    ->unsetRelation('roles')
                    ->unsetRelation('permissions');
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        return $next($request);
    }
}
