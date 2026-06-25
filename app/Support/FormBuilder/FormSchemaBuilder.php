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
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;

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
        $components = [];

        foreach ($definitions as $definition) {
            $component = static::buildSingle($definition, $definitions);

            if ($component !== null) {
                $components[] = $component;
            }
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

        if (! $name && $type !== FieldType::Heading) {
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
            FieldType::Heading => Placeholder::make($definition['name'] ?? ('heading_'.md5($definition['label'] ?? uniqid())))
                ->hiddenLabel()
                ->content(fn () => new \Illuminate\Support\HtmlString(
                    '<div class="text-lg font-bold">'.e($definition['label'] ?? '').'</div>'
                )),
        };

        if ($type === FieldType::Heading) {
            return static::applyVisibility($component, $definition);
        }

        $component
            ->label($definition['label'] ?? str($name)->headline()->toString())
            ->helperText($definition['help_text'] ?? null)
            ->placeholder($definition['placeholder'] ?? null)
            ->columnSpan($definition['column_span'] ?? 'full');

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
            ->mapWithKeys(fn (array $opt) => [$opt['value'] => $opt['label']])
            ->all();
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
