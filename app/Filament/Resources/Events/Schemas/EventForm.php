<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Event Name'),

                \Filament\Forms\Components\DateTimePicker::make('event_date')
                    ->label('Event Date'),

                \Filament\Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Invitation Expiry')
                    ->nullable(),
                \Filament\Forms\Components\TextInput::make('capacity')
                    ->label('Capacity')
                    ->numeric()
                    ->nullable(),
            ]);
    }
}
