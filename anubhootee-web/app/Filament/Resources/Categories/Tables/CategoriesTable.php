<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('home_order')
            ->reorderable('home_order')
            ->columns([
                ImageColumn::make('cover')->disk('public')->label('')->imageHeight(40),
                TextColumn::make('name')->searchable()
                    ->description(fn (Category $r) => $r->name_mr),
                TextColumn::make('big_title')->label('Chapter title')
                    ->description(fn (Category $r) => $r->deva_word),
                ToggleColumn::make('show_on_home')->label('On homepage'),
                TextColumn::make('posts_count')->counts('posts')->label('Posts'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
