<?php

namespace App\Filament\Resources\Guests\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GuestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('event_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('mobile')
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                TextInput::make('max_seats')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('confirmed_seats')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'viewed' => 'Viewed',
            'confirmed' => 'Confirmed',
            'declined' => 'Declined',
            'cancelled' => 'Cancelled',
        ])
                    ->default('pending')
                    ->required(),
                TextInput::make('invite_code')
                    ->required(),
                DateTimePicker::make('viewed_at'),
                DateTimePicker::make('responded_at'),
            ]);
    }
}
