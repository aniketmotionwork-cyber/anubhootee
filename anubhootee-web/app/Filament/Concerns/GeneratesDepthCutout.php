<?php

namespace App\Filament\Concerns;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;

/**
 * Create/Edit pages: after saving, make the depth-effect cutout for a new cover photo.
 * Needs Node.js on the server; without it the page says how to do it on your PC.
 */
trait GeneratesDepthCutout
{
    protected function afterCreate(): void
    {
        $this->makeDepthCutout();
    }

    protected function afterSave(): void
    {
        $this->makeDepthCutout();
    }

    protected function makeDepthCutout(): void
    {
        $record = $this->getRecord()->refresh();
        if (! $record->cover || $record->cover_fg) {
            return;
        }

        try {
            $code = Artisan::call('anubhootee:depth');
        } catch (\Throwable $e) {
            $code = 1;
        }
        $record->refresh();

        if ($code === 0 && $record->cover_fg) {
            Notification::make()->success()->title('Depth effect ready')
                ->body($record->depthUrl()
                    ? 'The subject of the cover photo will appear in front of the title.'
                    : 'No clear subject was found in this photo, so the title stays in front.')
                ->send();
        } else {
            Notification::make()->warning()->title('Depth effect not created')
                ->body('Run “php artisan anubhootee:depth” on a computer with Node.js, or upload a cutout yourself.')
                ->send();
        }
    }
}
