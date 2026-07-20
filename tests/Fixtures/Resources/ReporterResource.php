<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures\Resources;

use App\Models\Reporter;
use Filament\Resources\Resource;

class ReporterResource extends Resource
{
    protected static ?string $model = Reporter::class;
}
