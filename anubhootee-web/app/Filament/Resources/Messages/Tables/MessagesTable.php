<?php

namespace App\Filament\Resources\Messages\Tables;

use App\Models\Message;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable()
                    ->weight(fn (Message $r) => $r->read_at ? null : 'bold')
                    ->description(fn (Message $r) => $r->email),
                TextColumn::make('subject')->searchable()->placeholder('(no subject)')
                    ->description(fn (Message $r) => str($r->message)->limit(80)),
                TextColumn::make('created_at')->label('Received')->since()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('read_at')->label('Status')->nullable()
                    ->trueLabel('Read')->falseLabel('Unread'),
            ])
            ->recordActions([
                ViewAction::make()->label('Read')
                    ->mutateRecordDataUsing(function (array $data, Message $record) {
                        if (! $record->read_at) {
                            $record->update(['read_at' => now()]);
                        }

                        return $data;
                    }),
                Action::make('reply')->label('Reply')->icon('heroicon-o-envelope')
                    ->url(fn (Message $r) => 'mailto:'.$r->email.'?subject='.rawurlencode('Re: '.($r->subject ?: 'Anubhootee'))),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
