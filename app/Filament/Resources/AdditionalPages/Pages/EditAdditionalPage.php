<?php

namespace App\Filament\Resources\AdditionalPages\Pages;

use App\Filament\Resources\AdditionalPages\AdditionalPageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdditionalPage extends EditRecord
{
    protected static string $resource = AdditionalPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
