<?php

namespace App\Filament\Resources\AdditionalPages\Pages;

use App\Filament\Resources\AdditionalPages\AdditionalPageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdditionalPages extends ListRecords
{
    protected static string $resource = AdditionalPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
