<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Services\BulkSMSBDService;
use App\Support\OrderMessageTemplates;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Illuminate\Support\HtmlString;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_message')
                ->label('Send Message')
                ->icon('heroicon-o-paper-airplane')
                ->action(function ($record, $data) {

                    /** @var Order $order */
                    $order = $this->record;

                    $message = OrderMessageTemplates::parseOrderMessage($data['custom_message'], $order);

                    $SMS = app(BulkSMSBDService::class);
                    $response = $SMS->send([$order->customer_phone], $message);

                    if ($response->successful()) {

                        Notification::make()
                            ->success()
                            ->title('SMS has been sent successfully')
                            ->send();
                    } else {
                        Notification::make()
                            ->danger()
                            ->title('SMS failed')
                            ->body($response->errorLabel())
                            ->send();
                    }
                })
                ->schema([
                    Section::make('Quick Message Center')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->schema([
                            Select::make('message_template')
                                ->label('Choose Template')
                                ->options(
                                    collect(OrderMessageTemplates::all())
                                        ->mapWithKeys(fn($msg, $key) => [$key => ucfirst(str_replace('_', ' ', $key))])
                                )
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    if ($state) {
                                        $templates = OrderMessageTemplates::all();
                                        $msg = $templates[$state] ?? '';

                                        $set('custom_message', $msg);
                                    }
                                })
                                ->live(),

                            Textarea::make('custom_message')
                                ->label('Message')
                                ->rows(4)
                                ->helperText(new HtmlString('
                                <div class="text-xs space-y-2">
                                    <p class="font-semibold">Available Dynamic Values:</p>
                                    <pre class="whitespace-pre-wrap">
{customer_name}
{order_number}
{order_status}
{delivery_status}
{work_process}
{shipment_type}
{order_receive_date}
{order_place_date}
{delivery_date}
{total_price}
{shipping_charge}
{advance_payment}
{due_payment}
{product_weight}
{products}
                                    </pre>
                                </div>
                                '))
                        ])
                ]),
            DeleteAction::make(),
        ];
    }
}
