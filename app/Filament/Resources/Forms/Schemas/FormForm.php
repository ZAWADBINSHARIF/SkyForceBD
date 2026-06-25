<?php

namespace App\Filament\Resources\Forms\Schemas;

use App\Support\FormBuilder\ConditionOperator;
use App\Support\FormBuilder\FieldType;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class FormForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Form Builder')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema(static::detailsFields()),

                        Tab::make('Fields')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([static::fieldsRepeater()]),

                        Tab::make('Settings')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema(static::settingsFields()),
                    ]),
            ]);
    }

    protected static function detailsFields(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $state, ?string $old) {
                    // Auto-suggest a slug only while the user hasn't customized it themselves.
                    if (blank($get('slug')) || $get('slug') === Str::slug($old)) {
                        $set('slug', Str::slug($state));
                    }
                })
                ->maxLength(255),

            TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->helperText('Used in the public form URL, e.g. /forms/your-slug'),

            Textarea::make('description')
                ->rows(3)
                ->columnSpanFull(),

            Toggle::make('is_active')
                ->label('Form is active')
                ->default(true),
        ];
    }

    protected static function settingsFields(): array
    {
        return [
            TextInput::make('settings.submit_button_text')
                ->label('Submit button text')
                ->default('Submit'),

            TextInput::make('settings.success_message')
                ->label('Success message')
                ->default('Thanks! Your response has been recorded.'),

            \Filament\Forms\Components\DateTimePicker::make('starts_at')
                ->label('Opens at')
                ->helperText('Leave blank to open immediately'),

            \Filament\Forms\Components\DateTimePicker::make('ends_at')
                ->label('Closes at')
                ->helperText('Leave blank to keep open indefinitely'),

            TextInput::make('submission_limit')
                ->numeric()
                ->helperText('Leave blank for unlimited submissions'),
        ];
    }

    /**
     * The heart of the builder: a Repeater where each item represents one
     * form field, including a nested condition sub-form.
     */
    protected static function fieldsRepeater(): Repeater
    {
        return Repeater::make('fields')
            ->label('Form Fields')
            ->addActionLabel('Add field')
            ->reorderableWithButtons()
            ->collapsible()
            ->cloneable()
            ->itemLabel(fn(array $state): ?string => ($state['label'] ?? null)
                ? ($state['label'] . ' — ' . (FieldType::tryFrom($state['type'] ?? '')?->label() ?? $state['type'] ?? ''))
                : 'New field')
            ->schema([
                Section::make('Field')
                    ->columns(2)
                    ->schema([
                        Select::make('type')
                            ->label('Field type')
                            ->options(FieldType::options())
                            ->required()
                            ->live()
                            ->native(false),

                        TextInput::make('label')
                            ->label('Field label')
                            ->live(onBlur: true)
                            ->required()
                            ->afterStateUpdated(fn(Get $get, Set $set, ?string $state) => $set(
                                'name',
                                $get('name') ?: Str::slug($state ?? '', '_')
                            )),

                        TextInput::make('name')
                            ->label('Field key')
                            ->helperText('Used internally as the data key. Letters, numbers, underscores only.')
                            ->required(fn(Get $get) => FieldType::tryFrom($get('type') ?? '')?->isInput() ?? true)
                            ->visible(fn(Get $get) => FieldType::tryFrom($get('type') ?? '')?->isInput() ?? true)
                            ->rule('regex:/^[a-zA-Z0-9_]+$/')
                            ->maxLength(100),

                        TextInput::make('step')
                            ->label('Step')
                            ->helperText('Optional step name for multi-step public forms')
                            ->columnSpan(2),

                        Select::make('column_span')
                            ->label('Column span')
                            ->options([
                                'full' => 'Full width',
                                2 => 'Half width',
                            ])
                            ->default('full')
                            ->helperText('Choose how much horizontal space this field occupies on the public form'),

                        TextInput::make('placeholder')
                            ->visible(fn(Get $get) => FieldType::tryFrom($get('type') ?? '')?->isInput() ?? true),

                        Textarea::make('help_text')
                            ->label('Helper text')
                            ->rows(2)
                            ->columnSpanFull()
                            ->visible(fn(Get $get) => FieldType::tryFrom($get('type') ?? '')?->isInput() ?? true),

                        Textarea::make('validation_rules')
                            ->label('Validation rules')
                            ->helperText('Enter Laravel validation rules separated by pipes, e.g. required|max:255')
                            ->columnSpanFull()
                            ->visible(fn(Get $get) => FieldType::tryFrom($get('type') ?? '')?->isInput() ?? true),

                        TextInput::make('warning_text')
                            ->label('Warning text')
                            ->columnSpanFull(),

                        Select::make('warning_status')
                            ->label('Warning color')
                            ->options([
                                'warning' => 'Warning',
                                'danger' => 'Danger',
                                'info' => 'Info',
                                'success' => 'Success',
                            ])
                            ->default('warning')
                            ->visible(fn(Get $get) => filled($get('warning_text')))
                            ->columnSpan(2),

                        Textarea::make('description')
                            ->label('Section description')
                            ->rows(2)
                            ->columnSpanFull()
                            ->visible(fn(Get $get) => ($get('type') ?? '') === FieldType::Section->value),

                        Toggle::make('required')
                            ->label('Required by default')
                            ->helperText('Can be overridden below by a condition set to "Require"')
                            ->visible(fn(Get $get) => FieldType::tryFrom($get('type') ?? '')?->isInput() ?? true),

                        Repeater::make('options')
                            ->label('Options')
                            ->columnSpanFull()
                            ->addActionLabel('Add option')
                            ->visible(fn(Get $get) => FieldType::tryFrom($get('type') ?? '')?->hasOptions() ?? false)
                            ->schema([
                                TextInput::make('label')->required(),
                                TextInput::make('value')->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(2),
                    ]),

                static::conditionsSection(),
            ]);
    }

    /**
     * Per-field "Conditional Logic" sub-section: enable/disable, AND/OR
     * logic, an action (show vs require), and a repeater of comparison
     * rules referencing other fields in the SAME form by name.
     *
     * Note: this references sibling field names as free text rather than
     * a cross-repeater dropdown, since Filament repeaters can't easily
     * enumerate sibling items' live state for a dynamic select. Admins
     * type the field key (shown next to each field above) of the field
     * they want to depend on.
     */
    protected static function conditionsSection(): Section
    {
        return Section::make('Conditional Logic')
            ->icon('heroicon-o-adjustments-horizontal')
            ->collapsed()
            ->collapsible()
            ->schema([
                Toggle::make('conditions.enabled')
                    ->label('Enable conditional logic for this field')
                    ->live(),

                Select::make('conditions.action')
                    ->label('When conditions match...')
                    ->options([
                        'show' => 'Show this field (otherwise hide it)',
                        'require' => 'Require this field (otherwise optional)',
                    ])
                    ->default('show')
                    ->native(false)
                    ->visible(fn(Get $get) => $get('conditions.enabled')),

                Select::make('conditions.logic')
                    ->label('Match')
                    ->options([
                        'all' => 'ALL of the following rules (AND)',
                        'any' => 'ANY of the following rules (OR)',
                    ])
                    ->default('all')
                    ->native(false)
                    ->visible(fn(Get $get) => $get('conditions.enabled')),

                Repeater::make('conditions.rules')
                    ->label('Rules')
                    ->addActionLabel('Add rule')
                    ->visible(fn(Get $get) => $get('conditions.enabled'))
                    ->schema([
                        TextInput::make('field')
                            ->label('Other field key')
                            ->required()
                            ->helperText('The "Field key" of another field in this form'),

                        Select::make('operator')
                            ->options(ConditionOperator::options())
                            ->default('equals')
                            ->required()
                            ->live()
                            ->native(false),

                        TextInput::make('value')
                            ->label('Compare value')
                            ->helperText('For select/radio/checkbox fields, use the option\'s value, not its label')
                            ->visible(fn(Get $get) => (ConditionOperator::tryFrom($get('operator') ?? '')?->needsValue() ?? true)),
                    ])
                    ->columns(3)
                    ->defaultItems(1),
            ]);
    }
}
