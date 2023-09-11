<?php

namespace BezhanSalleh\FilamentShield\Middleware;

use BezhanSalleh\FilamentShield\Support\Utils;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class PanelAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!auth()->user()) return $next($request);
        
        $currentPanel = filament()->getCurrentPanel();
        $panels = filament()->getPanels();


        if (count($panels) > 1) {
            if (!$this->hasAccessTo($currentPanel)) {
                foreach ($panels as $key => $panel) {
                    if ($this->hasAccessTo($panel)) {
                        return redirect($panel->getPath());
                    }
                }
            }
        }

        return $next($request);
    }

    protected function hasAccessTo($panel)
    {
        $prepend = Str::of(Utils::getPanelPermissionPrefix())->append('_');
        $permissionName = Str::of($panel->getId())
            ->prepend($prepend);

        return auth()->user()->can($permissionName);
    }
}
