<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures\Resources;

use App\Models\Blog\Comment;
use Filament\Resources\Resource;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;
}
