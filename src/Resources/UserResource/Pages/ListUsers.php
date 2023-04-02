<?php

namespace BezhanSalleh\FilamentShield\Resources\UserResource\Pages;

use BezhanSalleh\FilamentShield\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
}
