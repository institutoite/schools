<?php

namespace App\Filament\Resources\AmbienteResource\Pages;

use App\Filament\Resources\AmbienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAmbientes extends ListRecords
{
    protected static string $resource = AmbienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
