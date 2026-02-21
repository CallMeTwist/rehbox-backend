<?php

namespace App\Filament\Resources\ShopItemResource\Pages;

use App\Filament\Resources\ShopItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShopItem extends EditRecord
{
    protected static string $resource = ShopItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
