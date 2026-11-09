<?php

namespace App\Filament\Resources\Messages\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name'),
                TextInput::make('email'),
                TextInput::make('subject')->columnSpanFull(),
                Textarea::make('message')->rows(10)->columnSpanFull(),
            ]);
    }
}
