<?php

namespace App\Filament\Resources\JsonResource\Pages;

use App\Filament\Resources\JsonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJsons extends ListRecords
{
    protected static string $resource = JsonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
