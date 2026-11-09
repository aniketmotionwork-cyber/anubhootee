<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use App\Filament\Concerns\GeneratesDepthCutout;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    use GeneratesDepthCutout;

    protected static string $resource = PostResource::class;
}
