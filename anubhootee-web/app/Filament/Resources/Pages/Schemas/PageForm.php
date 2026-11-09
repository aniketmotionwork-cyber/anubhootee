<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        $toolbar = [['bold', 'italic', 'link'], ['h2', 'h3'], ['blockquote', 'bulletList', 'orderedList'], ['attachFiles'], ['undo', 'redo']];

        return $schema
            ->columns(3)
            ->components([
                Tabs::make('Language')->columnSpan(2)->tabs([
                    Tab::make('English')->schema([
                        TextInput::make('title')->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state, string $operation) {
                                if ($operation === 'create' || blank($get('slug'))) {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        RichEditor::make('body')->label('Content')->toolbarButtons($toolbar)
                            ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('pages')
                            ->extraInputAttributes(['style' => 'min-height: 20rem']),
                    ]),
                    Tab::make('मराठी')->schema([
                        TextInput::make('title_mr')->label('Title (Marathi)'),
                        RichEditor::make('body_mr')->label('Content (Marathi)')->toolbarButtons($toolbar)
                            ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('pages')
                            ->extraInputAttributes(['style' => 'min-height: 20rem']),
                    ]),
                ]),
                Grid::make(1)->columnSpan(1)->schema([
                    Section::make('Settings')->schema([
                        TextInput::make('slug')->required()->unique(ignoreRecord: true)
                            ->helperText('Web address: /your-slug. Keep "contact" for the contact page.'),
                        Toggle::make('is_published')->label('Published')->default(true),
                        Toggle::make('in_menu')->label('Show in header menu'),
                        Toggle::make('in_footer')->label('Show in footer links'),
                        TextInput::make('sort')->label('Order')->numeric()->default(0),
                    ]),
                    Section::make('Cover photo')->schema([
                        FileUpload::make('cover')->hiddenLabel()->image()->disk('public')->directory('pages')->imageEditor(),
                    ]),
                ]),
            ]);
    }
}
