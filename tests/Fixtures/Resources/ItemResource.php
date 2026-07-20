<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures\Resources;

use Filament\Resources\Resource;
use Modules\Blog\Models\Item;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;
}
