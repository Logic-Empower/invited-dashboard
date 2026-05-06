<?php

namespace App\Filament\Resources\Guests\Schemas;

use App\Models\Guest;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GuestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('event_id')
                    ->numeric(),
                TextEntry::make('name'),
                TextEntry::make('mobile')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('max_seats')
                    ->numeric(),
                TextEntry::make('confirmed_seats')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('invite_code'),
                TextEntry::make('viewed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('responded_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Guest $record): bool => $record->trashed()),
            ]);
    }
}
