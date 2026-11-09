<?php

namespace App\Filament\Resources\Subscribers\Pages;

use App\Filament\Resources\Subscribers\SubscriberResource;
use App\Models\Subscriber;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListSubscribers extends ListRecords
{
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')->label('Download CSV')->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return response()->streamDownload(function () {
                        $out = fopen('php://output', 'w');
                        fputcsv($out, ['email', 'language', 'joined']);
                        foreach (Subscriber::orderBy('created_at')->cursor() as $s) {
                            fputcsv($out, [$s->email, $s->lang, $s->created_at?->toDateString()]);
                        }
                        fclose($out);
                    }, 'anubhootee-subscribers.csv');
                }),
        ];
    }
}
