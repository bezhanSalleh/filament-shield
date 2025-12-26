<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures\Models;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use HasPanelShield;
    use HasRoles;

    protected $guarded = [];

    protected $hidden = ['password', 'remember_token'];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    protected static function newFactory(): \BezhanSalleh\FilamentShield\Tests\database\factories\UserFactory
    {
        return \BezhanSalleh\FilamentShield\Tests\database\factories\UserFactory::new();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
