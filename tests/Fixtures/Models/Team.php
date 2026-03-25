<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures\Models;

use BezhanSalleh\FilamentShield\Tests\database\factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    protected static function newFactory(): TeamFactory
    {
        return TeamFactory::new();
    }
}
