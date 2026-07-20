<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures\Resources;

use App\Models\Editor;
use Filament\Resources\Resource;

class EditorResource extends Resource
{
    protected static ?string $model = Editor::class;
}
