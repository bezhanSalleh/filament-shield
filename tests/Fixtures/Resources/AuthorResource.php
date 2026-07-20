<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures\Resources;

use App\Models\Author;
use Filament\Resources\Resource;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;
}
