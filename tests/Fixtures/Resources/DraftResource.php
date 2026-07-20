<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures\Resources;

use App\Models\Blog\Draft;
use Filament\Resources\Resource;

class DraftResource extends Resource
{
    protected static ?string $model = Draft::class;
}
