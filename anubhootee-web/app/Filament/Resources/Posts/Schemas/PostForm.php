<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\Post;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
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

class PostForm
{
    /** Toolbar for the story editor: headings, quotes, lists, links, images. */
    private const TOOLBAR = [
        ['bold', 'italic', 'link'],
        ['h2', 'h3'],
        ['blockquote', 'bulletList', 'orderedList'],
        ['attachFiles'],
        ['undo', 'redo'],
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Grid::make(1)->columnSpan(2)->schema([
                    Tabs::make('Language')->tabs([
                        Tab::make('English')->schema([
                            TextInput::make('title')->required()->maxLength(190)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state, string $operation) {
                                    if ($operation === 'create' || blank($get('slug'))) {
                                        $set('slug', Str::slug((string) $state));
                                    }
                                }),
                            TextInput::make('subtitle')->label('Subtitle')
                                ->helperText('The small line under the title, e.g. पावसाळ्यातला घाटमाथा'),
                            Textarea::make('summary')->rows(2)
                                ->helperText('One or two sentences for cards, search results and Google.'),
                            RichEditor::make('body')->label('Story')
                                ->toolbarButtons(self::TOOLBAR)
                                ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('posts/inline')
                                ->extraInputAttributes(['style' => 'min-height: 24rem']),
                        ]),
                        Tab::make('मराठी')->schema([
                            TextInput::make('title_mr')->label('Title (Marathi)')
                                ->helperText('Leave empty to show the English version on Marathi pages.'),
                            Textarea::make('summary_mr')->label('Summary (Marathi)')->rows(2),
                            RichEditor::make('body_mr')->label('Story (Marathi)')
                                ->toolbarButtons(self::TOOLBAR)
                                ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('posts/inline')
                                ->extraInputAttributes(['style' => 'min-height: 24rem']),
                        ]),
                    ]),

                    Section::make('Route facts')
                        ->description('Small facts shown in a row: Distance · Drive time · Best season … For films: Runtime · Language · Year.')
                        ->collapsible()
                        ->schema([
                            Repeater::make('facts')->hiddenLabel()
                                ->schema([
                                    TextInput::make('label')->required()->placeholder('Distance'),
                                    TextInput::make('value')->required()->placeholder('186 km'),
                                ])
                                ->columns(2)->maxItems(8)->reorderable()->defaultItems(0)
                                ->addActionLabel('Add a fact'),
                        ]),

                    Section::make('Important tips')
                        ->collapsible()
                        ->schema([
                            Repeater::make('tips')->hiddenLabel()
                                ->schema([
                                    TextInput::make('title')->required()->placeholder('Start early.'),
                                    TextInput::make('text')->placeholder('Fog thickens after 3 pm.'),
                                ])
                                ->columns(2)->reorderable()->defaultItems(0)
                                ->addActionLabel('Add a tip'),
                        ]),
                ]),

                Grid::make(1)->columnSpan(1)->schema([
                    Section::make('Publish')->schema([
                        DateTimePicker::make('published_at')->label('Publish date')
                            ->native(false)->seconds(false)
                            ->helperText('Empty = draft. A future date schedules it.'),
                        Toggle::make('is_featured')->label('Featured'),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true)
                            ->helperText('The web address: /stories/your-slug'),
                    ]),
                    Section::make('Section')->schema([
                        Select::make('type')->options(Post::TYPES)->required()->default('story')->native(false)->live(),
                        Select::make('category_id')->label('Section')
                            ->relationship('category', 'name')->searchable()->preload()->required(),
                        Select::make('author_id')->label('Author')
                            ->relationship('author', 'name')->default(fn () => auth()->id()),
                        TextInput::make('location')->placeholder('Western Ghats'),
                        TagsInput::make('tags')->placeholder('Add a tag')->separator(','),
                    ]),
                    Section::make('Cover photo')->schema([
                        FileUpload::make('cover')->hiddenLabel()
                            ->image()->disk('public')->directory('posts')
                            ->imageEditor()->maxSize(8192)
                            ->helperText('Landscape, at least 1920 px wide. Shown full-screen.'),
                        TextInput::make('cover_credit')->label('Photo credit')
                            ->placeholder('Only if the photo is not yours'),
                        FileUpload::make('cover_fg')->label('Depth cutout')
                            ->image()->disk('public')->directory('depth')->acceptedFileTypes(['image/png', 'image/webp'])
                            ->helperText('Made automatically when you save: the photo\'s subject on a transparent background, shown in front of the title. Upload your own PNG/WebP (same size as the photo) to replace it.'),
                    ]),
                    Section::make('Video')
                        ->visible(fn (Get $get) => in_array($get('type'), ['vlog', 'documentary'], true))
                        ->schema([
                            TextInput::make('video_url')->label('YouTube or Instagram link')->url()
                                ->placeholder('https://www.youtube.com/watch?v=…'),
                        ]),
                ]),
            ]);
    }
}
