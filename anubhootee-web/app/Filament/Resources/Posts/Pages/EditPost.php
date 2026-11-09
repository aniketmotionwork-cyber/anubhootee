<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\DeleteAction;
use App\Filament\Concerns\GeneratesDepthCutout;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    use GeneratesDepthCutout;

    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
