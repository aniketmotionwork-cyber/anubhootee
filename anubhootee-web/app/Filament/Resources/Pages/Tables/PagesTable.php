<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('title')->searchable()->description(fn (Page $r) => '/'.$r->slug),
                IconColumn::make('title_mr')->label('मराठी')->boolean()
                    ->getStateUsing(fn (Page $r) => filled($r->title_mr)),
                ToggleColumn::make('in_menu')->label('Header'),
                ToggleColumn::make('in_footer')->label('Footer'),
                ToggleColumn::make('is_published')->label('Published'),
            ])
            ->recordActions([
                Action::make('view')->label('View')->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Page $r) => route('page', $r))->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
