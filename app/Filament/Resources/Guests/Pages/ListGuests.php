<?php

namespace App\Filament\Resources\Guests\Pages;

use App\Filament\Resources\Guests\GuestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListGuests extends ListRecords
{
    protected static string $resource = GuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }       
}
