<?php

namespace App\Filament\Pages;

use App\Enums\FieldLength;
use App\Enums\NavigationGroup;
use App\Models\Customer;
use App\Services\BulkSMSBDService;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use UnitEnum;

class SendSms extends Page
{

    use HasPageShield;

    protected string $view = 'filament.pages.send-sms';

    protected static string|BackedEnum|null $navigationIcon  = Heroicon::OutlinedChatBubbleLeftRight;
    protected static string|UnitEnum|null   $navigationGroup = NavigationGroup::UserManagement;
    protected static ?int                   $navigationSort  = 99;
    protected static ?string                $navigationLabel = 'Send SMS';
    protected static ?string                $title           = 'Send SMS';

    public ?array $data = [];

    // ── Computed ──────────────────────────────────────────────
    public int    $charCount      = 0;
    public int    $smsParts       = 0;
    public int    $recipientCount = 0;
    public float  $estimatedCost  = 0.0;
    public string $encoding       = 'GSM_7BIT_EX';

    const GSM_SINGLE_LIMIT   = 160;
    const GSM_MULTI_LIMIT    = 153;

    const UTF16_SINGLE_LIMIT = 70;
    const UTF16_MULTI_LIMIT  = 67;

    const COST_PER_PART      = 0.35;

    public function mount(): void
    {
        $this->form->fill([
            'phone_numbers' => [],
            'custom_numbers' => [],
            'message'       => '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([

                    // ── Stats ─────────────────────────────────
                    Section::make('')
                        ->schema([
                            Placeholder::make('stats')
                                ->label('')
                                ->content(fn(): HtmlString => new HtmlString($this->renderStats())),
                        ])
                        ->compact(),

                    // ── Recipients ────────────────────────────
                    Section::make('Recipients')
                        ->description('Select from customers or type numbers manually.')
                        ->icon(Heroicon::OutlinedUsers)
                        ->schema([

                            Select::make('phone_numbers')
                                ->label('Customer Numbers')
                                ->multiple()
                                ->searchable()
                                ->live()

                                ->afterStateUpdated(function (
                                    array $state,
                                    callable $set,
                                    callable $get
                                ): void {

                                    $normalized = normalize_phone_numbers($state);

                                    $set('phone_numbers', $normalized);

                                    $this->recipientCount = recipient_count(
                                        $normalized,
                                        $get('custom_numbers') ?? []
                                    );

                                    $this->recalculate();
                                })

                                ->getSearchResultsUsing(function (string $search): array {

                                    $normalizedSearch = $search;

                                    if (preg_match('/^[\d\+\s]+$/', $search)) {

                                        $normalizedSearch = head(
                                            normalize_phone_numbers([$search])
                                        ) ?: $search;
                                    }

                                    return Customer::query()
                                        ->whereNotNull('phone_number')
                                        ->where(function ($q) use ($search, $normalizedSearch) {

                                            $q->where('full_name', 'like', "%{$search}%")
                                                ->orWhere('phone_number', 'like', "%{$search}%")
                                                ->orWhere('phone_number', 'like', "%{$normalizedSearch}%");
                                        })
                                        ->limit(20)
                                        ->get()
                                        ->mapWithKeys(function (Customer $customer): array {

                                            $phone = head(
                                                normalize_phone_numbers([$customer->phone_number])
                                            ) ?: $customer->phone_number;

                                            return [
                                                $phone => "{$customer->full_name} — {$phone}",
                                            ];
                                        })
                                        ->toArray();
                                })

                                ->getOptionLabelsUsing(function (array $values): array {

                                    return collect(
                                        normalize_phone_numbers($values)
                                    )
                                        ->mapWithKeys(fn(string $phone): array => [
                                            $phone => $phone,
                                        ])
                                        ->toArray();
                                })

                                ->placeholder('Search by name or number…')
                                ->helperText('Example: 017XXXXXXXX or 88017XXXXXXXX')
                                ->columnSpanFull(),

                            TagsInput::make('custom_numbers')
                                ->label('Custom Numbers')
                                ->placeholder('Type number and press Enter')
                                ->splitKeys([',', ' ', ';'])
                                ->reorderable(false)
                                ->live()

                                ->nestedRecursiveRules([
                                    'regex:/^8801[3-9]\d{8}$/',
                                ])

                                ->afterStateUpdated(function (
                                    array $state,
                                    callable $set,
                                    callable $get
                                ): void {

                                    $normalized = normalize_phone_numbers($state);

                                    $set('custom_numbers', $normalized);

                                    $this->recipientCount = recipient_count(
                                        $get('phone_numbers') ?? [],
                                        $normalized
                                    );

                                    $this->recalculate();
                                })

                                ->helperText('Example: 017XXXXXXXX or 88017XXXXXXXX')
                                ->columnSpanFull(),

                        ]),

                    // ── Message ───────────────────────────────
                    Section::make('Message')
                        ->description('SMS encoding auto detection enabled.')
                        ->icon(Heroicon::OutlinedChatBubbleLeftEllipsis)
                        ->schema([

                            Textarea::make('message')
                                ->label('Message Text')
                                ->required()
                                ->rows(5)
                                ->maxLength(FieldLength::ExtraLong->value)
                                ->live(debounce: 300)

                                ->afterStateUpdated(function ($state) {

                                    $sms = $this->calculateSmsParts($state ?? '');

                                    $this->charCount = $sms['length'];
                                    $this->smsParts  = $sms['parts'];
                                    $this->encoding  = $sms['encoding'];

                                    $this->recalculate();
                                })

                                ->hint(fn() => $this->renderCharHint())
                                ->hintIcon(Heroicon::OutlinedCalculator)
                                ->columnSpanFull(),

                        ]),

                ])
                    ->livewireSubmitHandler('send')

                    ->footer([
                        Actions::make([
                            Action::make('send')
                                ->label('Send SMS')
                                ->icon(Heroicon::OutlinedPaperAirplane)
                                ->submit('send')
                                ->keyBindings(['mod+enter']),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    // ──────────────────────────────────────────────────────────
    // SMS Encoding Detection
    // ──────────────────────────────────────────────────────────

    private function isGsm7(string $text): bool
    {
        $gsm7 = '@£$¥èéùìòÇ
Øø
ÅåΔ_ΦΓΛΩΠΨΣΘΞ\\ÆæßÉ !"#¤%&\'()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà';

        $gsm7Extended = '^{}\\[~]|€';

        $all = $gsm7 . $gsm7Extended;

        for ($i = 0; $i < mb_strlen($text); $i++) {

            $char = mb_substr($text, $i, 1);

            if (! str_contains($all, $char)) {
                return false;
            }
        }

        return true;
    }

    private function calculateSmsParts(string $message): array
    {
        $isGsm = $this->isGsm7($message);

        if ($isGsm) {

            $singleLimit = self::GSM_SINGLE_LIMIT;
            $multiLimit  = self::GSM_MULTI_LIMIT;
            $encoding    = 'GSM_7BIT_EX';
        } else {

            $singleLimit = self::UTF16_SINGLE_LIMIT;
            $multiLimit  = self::UTF16_MULTI_LIMIT;
            $encoding    = 'UTF16';
        }

        $length = mb_strlen($message);

        $parts = match (true) {
            $length === 0 => 0,
            $length <= $singleLimit => 1,
            default => (int) ceil($length / $multiLimit),
        };

        return [
            'encoding' => $encoding,
            'length'   => $length,
            'parts'    => $parts,
            'limit'    => $parts > 1 ? $multiLimit : $singleLimit,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────

    private function recalculate(): void
    {
        $this->estimatedCost = round(
            $this->recipientCount
                * $this->smsParts
                * self::COST_PER_PART,
            2
        );
    }

    private function renderCharHint(): string
    {
        $message = $this->data['message'] ?? '';

        $sms = $this->calculateSmsParts($message);

        $limit = $sms['limit'];

        $partChar = $this->charCount % $limit;

        if ($partChar === 0 && $this->charCount > 0) {
            $partChar = $limit;
        }

        $remaining = $limit - $partChar;

        if ($this->charCount === 0) {
            return "0 / {$limit} chars";
        }

        return
            "{$partChar} / {$limit} chars · " .
            "{$this->smsParts} part(s) · " .
            "{$remaining} left · " .
            "Encoding: {$this->encoding} · " .
            "Total: {$this->charCount}";
    }

    private function renderStats(): string
    {
        $cost = number_format($this->estimatedCost, 2);

        return <<<HTML
        <div style="display:flex;gap:12px;flex-wrap:wrap;">

            <div style="flex:1;min-width:140px;background:var(--fi-color-gray-50);border-radius:8px;padding:12px 16px;">
                <p style="margin:0 0 4px;font-size:12px;color:var(--fi-color-gray-500);">
                    Recipients
                </p>

                <p style="margin:0;font-size:22px;font-weight:600;color:var(--fi-color-gray-900);">
                    {$this->recipientCount}
                </p>

                <p style="margin:2px 0 0;font-size:11px;color:var(--fi-color-gray-400);">
                    numbers selected
                </p>
            </div>

            <div style="flex:1;min-width:140px;background:var(--fi-color-gray-50);border-radius:8px;padding:12px 16px;">
                <p style="margin:0 0 4px;font-size:12px;color:var(--fi-color-gray-500);">
                    SMS Parts
                </p>

                <p style="margin:0;font-size:22px;font-weight:600;color:var(--fi-color-gray-900);">
                    {$this->smsParts}
                </p>

                <p style="margin:2px 0 0;font-size:11px;color:var(--fi-color-gray-400);">
                    {$this->encoding}
                </p>
            </div>

            <div style="flex:1;min-width:140px;background:var(--fi-color-gray-50);border-radius:8px;padding:12px 16px;">
                <p style="margin:0 0 4px;font-size:12px;color:var(--fi-color-gray-500);">
                    Estimated Cost
                </p>

                <p style="margin:0;font-size:22px;font-weight:600;color:var(--fi-color-gray-900);">
                    ৳{$cost}
                </p>

                <p style="margin:2px 0 0;font-size:11px;color:var(--fi-color-gray-400);">
                    ৳0.35 × parts × recipients
                </p>
            </div>

        </div>
        HTML;
    }

    // ──────────────────────────────────────────────────────────
    // Submit
    // ──────────────────────────────────────────────────────────

    public function send(): void
    {
        $data = $this->form->getState();

        $numbers = array_unique([
            ...($this->data['phone_numbers'] ?? []),
            ...($this->data['custom_numbers'] ?? []),
        ]);

        $message = trim($data['message'] ?? '');

        if (empty($numbers)) {

            Notification::make()
                ->warning()
                ->title('No recipients')
                ->body('Add at least one phone number.')
                ->send();

            return;
        }

        if (empty($message)) {

            Notification::make()
                ->warning()
                ->title('Empty message')
                ->body('Write a message before sending.')
                ->send();

            return;
        }

        $sms = app(BulkSMSBDService::class);

        $response = $sms->send($numbers, $message);

        if ($response->successful()) {

            Notification::make()
                ->success()
                ->title('SMS queued successfully')

                ->body(
                    count($numbers) . ' recipient(s) · ' .
                        $this->smsParts . ' part(s) · ' .
                        $this->encoding . ' · ' .
                        '৳' . number_format($this->estimatedCost, 2) . ' · ' .
                        'Message ID: ' . $response->messageId
                )

                ->send();
        } else {

            Notification::make()
                ->danger()
                ->title('SMS failed')
                ->body($response->errorLabel())
                ->send();
        }

        // Reset

        $this->form->fill([
            'phone_numbers' => [],
            'custom_numbers' => [],
            'message' => '',
        ]);

        $this->charCount      = 0;
        $this->smsParts       = 0;
        $this->recipientCount = 0;
        $this->estimatedCost  = 0.0;
        $this->encoding       = 'GSM_7BIT_EX';
    }
}
