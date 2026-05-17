<?php

namespace App\Filament\Resources\ProductPurchases\Pages;

use App\Filament\Resources\ProductPurchases\ProductPurchaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductPurchase extends EditRecord
{
    protected static string $resource = ProductPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
