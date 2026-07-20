<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures\Resources;

use App\Models\Blog\Article;
use Filament\Resources\Resource;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;
}
