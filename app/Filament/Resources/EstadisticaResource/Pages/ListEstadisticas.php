<?php

namespace App\Filament\Resources\EstadisticaResource\Pages;

use App\Filament\Resources\EstadisticaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstadisticas extends ListRecords
{
    protected static string $resource = EstadisticaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
