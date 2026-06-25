<?php

namespace App\Support\FormBuilder;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\HtmlString;

class FormSchemaBuilder
{
    /**
     * Build an array of live Filament Schema components from stored
     * field-definition JSON.
     *
     * @param  array<int, array>  $definitions
     * @return array<int, Component>
     */
    public static function build(array $definitions): array
    {
        $definitions = array_values($definitions);

        if (collect($definitions)->contains(fn(array $definition) => filled($definition['step'] ?? null))) {
            $steps = collect($definitions)
                ->groupBy(fn(array $definition) => $definition['step'] ?? 'General')
                ->map(function (\Illuminate\Support\Collection $fields, string $stepLabel) use ($definitions) {
                    return Step::make($stepLabel ?: 'General')
                        ->schema([
                            Grid::make(2)
                                ->schema($fields->flatMap(fn(array $definition) => static::buildSingleComponents($definition, $definitions))->all()),
                        ]);
                })
                ->values()
                ->all();

            return [Wizard::make($steps)->startOnStep(1)];
        }

        return [
            Grid::make(2)
                ->schema(collect($definitions)
                    ->flatMap(fn(array $definition) => static::buildSingleComponents($definition, $definitions))
                    ->all()),
        ];
    }

    protected static function buildSingleComponents(array $definition, array $allDefinitions): array
    {
        $component = static::buildSingle($definition, $allDefinitions);

        if (! $component) {
            return [];
        }

        $components = [$component];

        if (! empty($definition['warning_text'])) {
            $components[] = Callout::make()
                ->description($definition['warning_text'])
                ->status($definition['warning_status'] ?? 'warning');
        }

        return $components;
    }

    protected static function buildSingle(array $definition, array $allDefinitions): ?Component
    {
        $type = FieldType::tryFrom($definition['type'] ?? '');

        if (! $type) {
            return null;
        }

        $name = $definition['name'] ?? null;

        if (! $name && ! in_array($type, [FieldType::Heading, FieldType::Section], true)) {
            return null;
        }

        $component = match ($type) {
            FieldType::Text => TextInput::make($name),
            FieldType::Email => TextInput::make($name)->email(),
            FieldType::Number => TextInput::make($name)->numeric(),
            FieldType::Textarea => Textarea::make($name)->rows($definition['rows'] ?? 4),
            FieldType::Select => Select::make($name)
                ->options(static::optionsFor($definition))
                ->searchable(count($definition['options'] ?? []) > 8),
            FieldType::Radio => Radio::make($name)->options(static::optionsFor($definition)),
            FieldType::Checkbox => Checkbox::make($name),
            FieldType::CheckboxGroup => CheckboxList::make($name)->options(static::optionsFor($definition)),
            FieldType::Date => DatePicker::make($name),
            FieldType::DateTime => DateTimePicker::make($name),
            FieldType::FileUpload => FileUpload::make($name),
            FieldType::Toggle => Toggle::make($name),
            FieldType::Heading => Placeholder::make($definition['name'] ?? ('heading_' . md5($definition['label'] ?? uniqid())))
                ->hiddenLabel()
                ->content(fn() => new HtmlString(
                    '<div class="text-lg font-bold">' . e($definition['label'] ?? '') . '</div>'
                )),
            FieldType::Section => Placeholder::make($definition['name'] ?? ('section_' . md5($definition['label'] ?? uniqid())))
                ->hiddenLabel()
                ->content(fn() => new HtmlString(
                    '<div class="space-y-1">'
                        . '<div class="text-lg font-bold">' . e($definition['label'] ?? '') . '</div>'
                        . (filled($definition['description'] ?? '') ? '<div class="text-sm text-gray-500">' . e($definition['description']) . '</div>' : '')
                        . '</div>'
                )),
        };

        if (in_array($type, [FieldType::Heading, FieldType::Section], true)) {
            return static::applyVisibility($component, $definition)
                ->columnSpan(static::normalizeColumnSpan($definition['column_span'] ?? 'full'));
        }

        $component
            ->label($definition['label'] ?? str($name)->headline()->toString())
            ->helperText($definition['help_text'] ?? null)
            ->columnSpan(static::normalizeColumnSpan($definition['column_span'] ?? 'full'))
            ->rules($definition['validation_rules'] ?? []);

        if (in_array($type, [
            FieldType::Text,
            FieldType::Email,
            FieldType::Number,
            FieldType::Textarea,
        ], true)) {
            $component->placeholder($definition['placeholder'] ?? null);
        }

        // --- Conditional REQUIRED logic ---
        // Recomputed live as a closure so it reacts to other field changes
        // without a page reload. We only need $get() to fetch the specific
        // sibling fields this field's own conditions reference.
        $component->required(function (Get $get) use ($definition) {
            return ConditionEvaluator::isRequired($definition, static::stateFor($definition, $get));
        });

        // --- Conditional VISIBILITY logic ---
        $component = static::applyVisibility($component, $definition);

        // --- Reactivity wiring ---
        // Any field referenced as a *trigger* by another field's conditions
        // must be `live()` so dependent fields recompute instantly.
        if (static::isReferencedAsTrigger($name, $allDefinitions)) {
            $component->live(onBlur: in_array($type, [FieldType::Text, FieldType::Textarea, FieldType::Number, FieldType::Email]));
        }

        return $component;
    }

    protected static function applyVisibility(Component $component, array $definition): Component
    {
        return $component->visible(function (Get $get) use ($definition) {
            return ConditionEvaluator::isVisible($definition, static::stateFor($definition, $get));
        });
    }

    protected static function optionsFor(array $definition): array
    {
        return collect($definition['options'] ?? [])
            ->mapWithKeys(fn(array $opt) => [$opt['value'] => $opt['label']])
            ->all();
    }

    protected static function normalizeColumnSpan(int|string|null $columnSpan): int|string
    {
        if ($columnSpan === null) {
            return 'full';
        }

        if ((string) $columnSpan === '2') {
            return 1;
        }

        return $columnSpan;
    }

    /**
     * Whether $fieldName is referenced inside any other field's
     * conditions.rules[].field — meaning it must be `live()`.
     */
    protected static function isReferencedAsTrigger(?string $fieldName, array $allDefinitions): bool
    {
        if (! $fieldName) {
            return false;
        }

        foreach ($allDefinitions as $def) {
            $rules = $def['conditions']['rules'] ?? [];

            foreach ($rules as $rule) {
                if (($rule['field'] ?? null) === $fieldName) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Build a small ['trigger_field_name' => value, ...] map by explicitly
     * $get()-ing only the sibling fields that THIS field's own conditions
     * reference. This avoids relying on any "fetch entire form state" trick
     * and uses only the documented per-key $get('field') API.
     */
    protected static function stateFor(array $definition, Get $get): array
    {
        $rules = $definition['conditions']['rules'] ?? [];
        $state = [];

        foreach ($rules as $rule) {
            $field = $rule['field'] ?? null;

            if ($field) {
                $state[$field] = $get($field);
            }
        }

        return $state;
    }
}
