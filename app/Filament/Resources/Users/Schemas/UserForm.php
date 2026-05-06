<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('role')
                    ->options(['admin' => 'Admin', 'client' => 'Client'])
                    ->default('client')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('mobile')
                    ->label('Mobile Number')
                    ->tel()
                    ->placeholder('e.g. +96170500560'),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                    ->revealable(), // Allows users to see what was generated
                Select::make('status')
                    ->options(['enabled' => 'Enabled', 'expired' => 'Expired', 'disabled' => 'Disabled'])
                    ->default('enabled')
                    ->required(),
            ]);
    }
}
