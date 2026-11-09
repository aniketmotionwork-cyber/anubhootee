<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Section')->columnSpan(1)->schema([
                    TextInput::make('name')->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state, string $operation) {
                            if ($operation === 'create' || blank($get('slug'))) {
                                $set('slug', Str::slug((string) $state));
                            }
                        }),
                    TextInput::make('name_mr')->label('Name (Marathi)')->placeholder('प्रवास'),
                    TextInput::make('slug')->required()->unique(ignoreRecord: true)
                        ->helperText('Web address: /category/your-slug'),
                    Textarea::make('description')->rows(2),
                    Textarea::make('description_mr')->label('Description (Marathi)')->rows(2),
                ]),

                Section::make('Homepage chapter')
                    ->description('Full-screen chapters people drag through on the homepage.')
                    ->columnSpan(1)
                    ->schema([
                        Toggle::make('show_on_home')->label('Show on homepage')->live(),
                        TextInput::make('home_order')->label('Chapter order')->numeric()->default(1)
                            ->helperText('1 = first'),
                        TextInput::make('big_title')->label('Big title')->placeholder('Rasta')
                            ->helperText('Large word on the homepage. Empty = section name.'),
                        TextInput::make('deva_word')->label('Devanagari word')->placeholder('रस्ता'),
                        TextInput::make('cta')->label('Button text')->placeholder('Explore routes'),
                        TextInput::make('cta_mr')->label('Button text (Marathi)')->placeholder('मार्ग पहा'),
                        TextInput::make('link')->label('Button link')
                            ->placeholder('/about or https://…')
                            ->helperText('Optional. Empty = open this section.'),
                        FileUpload::make('cover')->label('Cover photo')
                            ->image()->disk('public')->directory('chapters')->imageEditor()->maxSize(8192)
                            ->required(fn (Get $get) => (bool) $get('show_on_home'))
                            ->helperText('Landscape, at least 1920 px wide. Needed for the homepage.'),
                        TextInput::make('cover_credit')->label('Photo credit'),
                        FileUpload::make('cover_fg')->label('Depth cutout')
                            ->image()->disk('public')->directory('depth')->acceptedFileTypes(['image/png', 'image/webp'])
                            ->helperText('Made automatically when you save. The subject appears in front of the big chapter title, like an iPhone lock screen.'),
                    ]),
            ]);
    }
}
