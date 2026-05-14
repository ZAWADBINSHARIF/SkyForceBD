<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Enums\TransactionStatus;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Transaction;
use App\Services\SSLCommerzService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncPayment')
                ->label('Sync Payment Status')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->requiresConfirmation()
                ->action(function () {

                    $record = $this->record;

                    $tranId = data_get($record->payment_info, 'tran_id');

                    if (! $tranId) {
                        Notification::make()
                            ->title('Transaction ID not found')
                            ->danger()
                            ->send();

                        return;
                    }

                    $ssl = app(SSLCommerzService::class);
                    $element = $ssl->getValidTransaction($tranId);

                    if ($element === null) {
                        Notification::make()
                            ->title('Invalid transaction from SSLCommerz')
                            ->danger()
                            ->send();

                        return redirect()->route('checkout.fail');
                    }

                    if ($element->isHighRisk()) {
                        Log::warning('SSLCommerz high risk transaction', [
                            'tran_id' => $tranId,
                        ]);
                    }

                    Transaction::where('transaction_number', $tranId)
                        ->update([
                            'status'              => TransactionStatus::Success->value,
                            'validation_id'       => $element->valId,
                            'bank_transaction_id' => $element->bankTranId,
                            'payment_method'      => $element->cardBrand,
                            'payment_amount'      => $element->amount,
                            'store_amount'        => $element->storeAmount,
                            'card_brand'          => $element->cardBrand,
                            'card_issuer_country' => $element->cardIssuerCountry,
                            'payment_info'        => $record->payment_info,
                        ]);

                    Notification::make()
                        ->title('Payment updated successfully')
                        ->success()
                        ->send();

                    return redirect(request()->header('Referer'));
                }),
            DeleteAction::make(),
        ];
    }
}
