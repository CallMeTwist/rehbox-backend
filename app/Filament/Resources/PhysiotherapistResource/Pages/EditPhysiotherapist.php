<?php

namespace App\Filament\Resources\PhysiotherapistResource\Pages;

use App\Filament\Resources\PhysiotherapistResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPhysiotherapist extends EditRecord
{
    protected static string $resource = PhysiotherapistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
