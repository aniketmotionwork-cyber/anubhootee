<?php

namespace App\Filament\Resources\Messages;

use App\Filament\Resources\Messages\Pages\ListMessages;
use App\Filament\Resources\Messages\Schemas\MessageForm;
use App\Filament\Resources\Messages\Tables\MessagesTable;
use App\Models\Message;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Contact-form messages. Read-only: open one to read it, reply by email.
 */
class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static string|\UnitEnum|null $navigationGroup = 'Inbox';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $unread = Message::whereNull('read_at')->count();

        return $unread ? (string) $unread : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return MessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MessagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMessages::route('/'),
        ];
    }
}
