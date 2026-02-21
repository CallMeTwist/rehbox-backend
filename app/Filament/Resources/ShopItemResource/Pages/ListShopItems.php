<?php

namespace App\Filament\Resources\ShopItemResource\Pages;

use App\Filament\Resources\ShopItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShopItems extends ListRecords
{
    protected static string $resource = ShopItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
