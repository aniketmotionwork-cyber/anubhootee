<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('cover')->disk('public')->label('')->square()->imageSize(48),
                TextColumn::make('title')->searchable(['title', 'title_mr', 'subtitle'])->wrap()
                    ->description(fn (Post $r) => $r->subtitle),
                TextColumn::make('type')->badge()
                    ->formatStateUsing(fn (string $state) => Post::TYPES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'vlog', 'documentary' => 'warning',
                        'diary' => 'gray',
                        default => 'primary',
                    }),
                TextColumn::make('category.name')->label('Section')->sortable(),
                IconColumn::make('title_mr')->label('मराठी')->boolean()
                    ->getStateUsing(fn (Post $r) => filled($r->title_mr)),
                IconColumn::make('is_featured')->label('Featured')->boolean()->toggleable(),
                TextColumn::make('published_at')->label('Published')->date('j M Y')->sortable()
                    ->placeholder('Draft'),
            ])
            ->filters([
                SelectFilter::make('type')->options(Post::TYPES),
                SelectFilter::make('category_id')->label('Section')->relationship('category', 'name'),
                TernaryFilter::make('published_at')->label('Status')->nullable()
                    ->trueLabel('Published')->falseLabel('Drafts'),
            ])
            ->recordActions([
                Action::make('view')->label('View')->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Post $r) => route('post', $r))->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
