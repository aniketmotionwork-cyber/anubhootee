<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Concerns\GeneratesDepthCutout;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    use GeneratesDepthCutout;

    protected static string $resource = CategoryResource::class;
}
