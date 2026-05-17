<?php

namespace App\Filament\Pages;

use App\Enums\FieldLength;
use App\Enums\NavigationGroup;
use App\Enums\StoragePath;
use App\Models\AboutUs;
use App\Models\AdditionalPage;
use App\Models\AdsVideo;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Office;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;
use UnitEnum;

class SiteSetting extends Page
{
    protected string $view = 'filament.pages.site-setting';

    protected static string|BackedEnum|null $navigationIcon  = Heroicon::OutlinedCog6Tooth;
    protected static string|UnitEnum|null   $navigationGroup = NavigationGroup::Website;
    protected static ?int                   $navigationSort  = 99;
    protected static ?string                $navigationLabel = 'Website Settings';
    protected static ?string                $title           = 'Website Settings';

    #[Url]
    public string $activeTab = 'contact';

    // ── One state bag per model ───────────────────────────────────
    public ?array $contactData  = [];
    public ?array $aboutData    = [];
    public ?array $countryData  = [];
    public ?array $videoData    = [];
    public ?array $officeData   = [];
    public ?array $adsVideoData = [];

    // ── Boot: fill each form from its model ───────────────────────
    public function mount(): void
    {
        $this->contactForm->fill(
            Contact::first()?->attributesToArray() ?? []
        );

        $this->aboutForm->fill(
            AboutUs::first()?->attributesToArray() ?? []
        );

        $this->countryForm->fill(
            Country::first()?->attributesToArray() ?? []
        );

        $this->officeForm->fill(
            Office::first()?->attributesToArray() ?? []
        );

        $this->adsVideoForm->fill(
            AdsVideo::first()?->attributesToArray() ?? []
        );
    }

    // ── Schema definitions ────────────────────────────────────────

    public function contactForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('Office Locations')
                        ->icon(Heroicon::OutlinedMapPin)
                        ->columns(2)
                        ->schema([
                            TextInput::make('head_office')
                                ->label('Head Office')
                                ->nullable()
                                ->maxLength(FieldLength::ExtraLong->value)
                                ->columnSpan(1),

                            TextInput::make('shop_office')
                                ->label('Shop Office')
                                ->nullable()
                                ->maxLength(FieldLength::ExtraLong->value)
                                ->columnSpan(1),

                            TextInput::make('licence')
                                ->label('Licence / Registration')
                                ->nullable()
                                ->maxLength(FieldLength::Default->value)
                                ->columnSpanFull(),
                        ]),

                    Section::make('Contact Details')
                        ->icon(Heroicon::OutlinedPhone)
                        ->columns(2)
                        ->schema([
                            TextInput::make('email')->email()->nullable()->maxLength(FieldLength::Default->value)->columnSpan(1),
                            TextInput::make('phone')->tel()->nullable()->maxLength(FieldLength::Short->value)->columnSpan(1),
                            TextInput::make('whatsapp')->tel()->nullable()->maxLength(FieldLength::Short->value)->columnSpan(1),
                        ]),

                    Section::make('Social Media')
                        ->icon(Heroicon::OutlinedShare)
                        ->columns(2)
                        ->schema([
                            TextInput::make('facebook')->url()->nullable()->maxLength(FieldLength::Long->value)->columnSpan(1),
                            TextInput::make('youtube')->url()->nullable()->maxLength(FieldLength::Long->value)->columnSpan(1),
                            TextInput::make('instagram')->url()->nullable()->maxLength(FieldLength::Long->value)->columnSpan(1),
                        ]),
                ])
                    ->livewireSubmitHandler('saveContact')
                    ->footer([
                        Actions::make([
                            Action::make('saveContact')
                                ->label('Save Contact')
                                ->submit('saveContact')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('contactData');
    }

    public function aboutForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('Hero Image')
                        ->icon(Heroicon::OutlinedPhoto)
                        ->schema([
                            FileUpload::make('image_url')
                                ->label('About Us Photo')
                                ->image()
                                ->disk('public')
                                ->directory(StoragePath::AboutImage->value)
                                ->maxSize(4096)
                                ->imageEditor()
                                ->columnSpanFull(),
                        ]),

                    Section::make('Heading')
                        ->icon(Heroicon::OutlinedPencil)
                        ->columns(2)
                        ->schema([
                            TextInput::make('heading')
                                ->label('Heading')
                                ->required()
                                ->maxLength(FieldLength::Default->value)
                                ->placeholder('We Deliver the Best')
                                ->columnSpan(1),

                            TextInput::make('heading_highlight')
                                ->label('Highlighted Text')
                                ->nullable()
                                ->maxLength(FieldLength::Default->value)
                                ->placeholder('Shopping Experience')
                                ->helperText('Rendered in primary colour.')
                                ->columnSpan(1),
                        ]),

                    Section::make('Body & Features')
                        ->icon(Heroicon::OutlinedBars3BottomLeft)
                        ->schema([
                            Textarea::make('body')
                                ->label('Body Paragraph')
                                ->required()
                                ->maxLength(FieldLength::ExtraLong->value)
                                ->rows(4)
                                ->columnSpanFull(),

                            Repeater::make('features')
                                ->label('Feature Bullet Points')
                                ->schema([
                                    TextInput::make('feature')
                                        ->label('Feature')
                                        ->required()
                                        ->maxLength(FieldLength::Long->value),
                                ])
                                ->addActionLabel('+ Add Feature')
                                ->reorderable()
                                ->collapsible()
                                ->columnSpanFull(),
                        ]),

                    Section::make('Call to Action')
                        ->icon(Heroicon::OutlinedCursorArrowRays)
                        ->columns(2)
                        ->schema([
                            TextInput::make('cta_label')
                                ->label('Button Label')
                                ->nullable()
                                ->maxLength(FieldLength::Short->value)
                                ->placeholder('Learn More')
                                ->columnSpan(1),

                            TextInput::make('cta_link')
                                ->label('Button URL')
                                ->url()
                                ->nullable()
                                ->maxLength(FieldLength::Long->value)
                                ->columnSpan(1),
                        ]),
                ])
                    ->livewireSubmitHandler('saveAbout')
                    ->footer([
                        Actions::make([
                            Action::make('saveAbout')
                                ->label('Save About Us')
                                ->submit('saveAbout')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('aboutData');
    }

    public function countryForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('Shopping Destinations')
                        ->description('Drag to reorder. Displayed on the homepage country carousel.')
                        ->icon(Heroicon::OutlinedGlobeAlt)
                        ->schema([
                            Repeater::make('country')
                                ->label('')
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Country Name')
                                        ->required()
                                        ->maxLength(FieldLength::Default->value)
                                        ->placeholder('Thailand')
                                        ->columnSpan(2),

                                    TextInput::make('code')
                                        ->label('Flag Code')
                                        ->required()
                                        ->maxLength(FieldLength::Tiny->value)
                                        ->placeholder('th')
                                        ->helperText('2-letter ISO — flagcdn.com/{code}.svg')
                                        ->columnSpan(1),

                                    Select::make('url')
                                        ->label('Linked Page')
                                        ->options(
                                            fn() => AdditionalPage::query()->where('published', true)->pluck('name', 'slug')
                                                ->mapWithKeys(fn($name, $slug) => ["/page/{$slug}" => $name])
                                        )
                                        ->searchable()
                                        ->nullable()
                                        ->columnSpan(2),
                                ])
                                ->columns(5)
                                ->addActionLabel('+ Add Country')
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(
                                    fn(array $state): string => ($state['code'] ? strtoupper($state['code']) . ' — ' : '') . ($state['name'] ?? 'New Country')
                                )
                                ->columnSpanFull(),
                        ]),
                ])
                    ->livewireSubmitHandler('saveCountries')
                    ->footer([
                        Actions::make([
                            Action::make('saveCountries')
                                ->label('Save Countries')
                                ->submit('saveCountries')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('countryData');
    }

    public function officeForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('Offices')
                        ->description('Manage office locations shown on the website. Drag to reorder.')
                        ->icon(Heroicon::OutlinedBuildingOffice2)
                        ->schema([
                            Repeater::make('office')
                                ->label('')
                                ->schema([
                                    TextInput::make('country')
                                        ->label('Country')
                                        ->required()
                                        ->maxLength(FieldLength::Default->value)
                                        ->placeholder('Bangladesh')
                                        ->columnSpan(1),

                                    TextInput::make('flag')
                                        ->label('Flag Emoji')
                                        ->required()
                                        ->maxLength(FieldLength::Tiny->value)
                                        ->placeholder('🇧🇩')
                                        ->columnSpan(1),

                                    TextInput::make('label')
                                        ->label('Office Label')
                                        ->required()
                                        ->maxLength(FieldLength::Default->value)
                                        ->placeholder('Head Office')
                                        ->columnSpan(1),

                                    TextInput::make('name')
                                        ->label('Office Name')
                                        ->required()
                                        ->maxLength(FieldLength::Default->value)
                                        ->placeholder('Sky Force BD HQ')
                                        ->columnSpan(1),

                                    FileUpload::make('image')
                                        ->label('Office Photo')
                                        ->image()
                                        ->disk('public')
                                        ->directory(StoragePath::OfficeImage->value)
                                        ->maxSize(4096)
                                        ->imageEditor()
                                        ->columnSpanFull(),

                                    TextInput::make('address')
                                        ->label('Address')
                                        ->required()
                                        ->maxLength(FieldLength::ExtraLong->value)
                                        ->placeholder('New Elephant Road, Popular Tower, 7th Floor, Dhaka 1205')
                                        ->columnSpanFull(),

                                    TextInput::make('phone')
                                        ->label('Phone')
                                        ->tel()
                                        ->nullable()
                                        ->maxLength(FieldLength::Short->value)
                                        ->placeholder('+880 1700-000000')
                                        ->columnSpan(1),

                                    TextInput::make('email')
                                        ->label('Email')
                                        ->email()
                                        ->nullable()
                                        ->maxLength(FieldLength::Default->value)
                                        ->placeholder('bd@skyforcebd.com')
                                        ->columnSpan(1),

                                    TextInput::make('hours')
                                        ->label('Working Hours')
                                        ->nullable()
                                        ->maxLength(FieldLength::Short->value)
                                        ->placeholder('Sat – Thu: 9:00 AM – 6:00 PM')
                                        ->columnSpan(1),

                                    TextInput::make('closed')
                                        ->label('Closed On')
                                        ->nullable()
                                        ->maxLength(FieldLength::Short->value)
                                        ->placeholder('Friday: Closed')
                                        ->columnSpan(1),

                                    TextInput::make('maps')
                                        ->label('Google Maps URL')
                                        ->url()
                                        ->nullable()
                                        ->maxLength(FieldLength::Long->value)
                                        ->placeholder('https://maps.google.com/?q=...')
                                        ->columnSpanFull(),
                                ])
                                ->columns(4)
                                ->addActionLabel('+ Add Office')
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(
                                    fn(array $state): string => ($state['flag'] ?? '') . ' ' . ($state['name'] ?? 'New Office')
                                )
                                ->columnSpanFull(),
                        ]),
                ])
                    ->livewireSubmitHandler('saveOffices')
                    ->footer([
                        Actions::make([
                            Action::make('saveOffices')
                                ->label('Save Offices')
                                ->submit('saveOffices')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('officeData');
    }

    public function adsVideoForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('Videos')
                        ->description('YouTube and Facebook video links shown on the homepage carousel.')
                        ->icon(Heroicon::OutlinedPlayCircle)
                        ->schema([
                            Repeater::make('video')
                                ->label('')
                                ->schema([
                                    TextInput::make('url')
                                        ->label('Video URL')
                                        ->url()
                                        ->required()
                                        ->maxLength(FieldLength::Long->value)
                                        ->placeholder('https://www.youtube.com/watch?v=...')
                                        ->helperText('Supports YouTube and Facebook URLs. Thumbnail is auto-generated on the frontend.')
                                        ->columnSpan(3),

                                    TextInput::make('title')
                                        ->label('Title')
                                        ->required()
                                        ->maxLength(FieldLength::Default->value)
                                        ->placeholder('How to request a custom product link')
                                        ->columnSpan(3),
                                ])
                                ->columns(6)
                                ->addActionLabel('+ Add Video')
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn(array $state): string => $state['title'] ?? 'New Video')
                                ->columnSpanFull(),
                        ]),
                ])
                    ->livewireSubmitHandler('saveAdsVideos')
                    ->footer([
                        Actions::make([
                            Action::make('saveAdsVideos')
                                ->label('Save Videos')
                                ->submit('saveAdsVideos')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('adsVideoData');
    }

    public function saveOffices(): void
    {
        $data = $this->officeForm->getState();

        Office::updateOrCreate(['id' => Office::first()?->id], [
            'office' => $data['office'] ?? [],
        ]);

        $this->notify('Offices saved.');
    }

    public function saveAdsVideos(): void
    {
        $data = $this->adsVideoForm->getState();

        AdsVideo::updateOrCreate(['id' => AdsVideo::first()?->id], [
            'video' => $data['video'] ?? [],
        ]);

        $this->notify('Videos saved.');
    }


    // ── Save handlers — one per model ────────────────────────────

    public function saveContact(): void
    {
        $data = $this->contactForm->getState();

        Contact::updateOrCreate(['id' => Contact::first()?->id], $data);

        $this->notify('Contact settings saved.');
    }

    public function saveAbout(): void
    {
        $data = $this->aboutForm->getState();

        AboutUs::updateOrCreate(['id' => AboutUs::first()?->id], $data);

        $this->notify('About Us saved.');
    }

    public function saveCountries(): void
    {
        $data = $this->countryForm->getState();

        Country::updateOrCreate(['id' => Country::first()?->id], [
            'country' => $data['country'] ?? [],
        ]);

        $this->notify('Countries saved.');
    }


    // ── Helper ────────────────────────────────────────────────────

    private function notify(string $title): void
    {
        Notification::make()
            ->success()
            ->title($title)
            ->send();
    }
}
