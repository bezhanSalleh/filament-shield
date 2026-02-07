<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Traits;

trait HasInjectedShieldPermissions
{
    // Hier landen die von der Page übergebenen Booleans
    public array $permissions = [];

    /**
     * Einfacher Check: $this->allows('insertSick')
     */
    public function canShield(string $action): bool
    {
        return $this->permissions[$action] ?? false;
    }

    /**
     * Optional: Mount-Check für die gesamte Komponente
     */
    public function authorizeComponent(string $requiredAction = 'view'): void
    {
        if (! $this->allows($requiredAction)) {
            abort(403);
        }
    }
}
