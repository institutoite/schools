<?php

namespace App\Filament\Resources\EstadisticaResource\Pages;

use App\Filament\Resources\EstadisticaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEstadistica extends EditRecord
{
    protected static string $resource = EstadisticaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
