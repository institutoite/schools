<?php

namespace App\Filament\Resources\ColegioResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\Colegio;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Storage;
use Livewire\TemporaryUploadedFile;


class ImportarColegios extends Widget  implements HasForms
{

     use InteractsWithForms;

    protected static string $view = 'filament.widgets.importar-colegios';
    protected int|string|array $columnSpan = 'full';

    public $jsonFile;

    protected function getFormSchema(): array
    {
        return [
            FileUpload::make('jsonFile')
                ->label('Subir archivo JSON')
                ->required()
                ->acceptedFileTypes(['application/json'])
                ->directory('importaciones')
                ->preserveFilenames()
                ->maxSize(1024),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();
        
        /** @var TemporaryUploadedFile $file */
        $file = $data['jsonFile'];
        $filePath = $file->getRealPath();
        $content = file_get_contents($filePath);
        $colegiosData = json_decode($content, true);

        // Aquí añade tu lógica de importación
        // ...

        // Notificación de éxito
        \Filament\Notifications\Notification::make()
            ->title('Importación completada')
            ->success()
            ->send();
    }

}
