<?php

namespace App\Support\FormBuilder;

use Illuminate\Support\Str;

enum ConditionOperator: string
{
    case Equals = 'equals';
    case NotEquals = 'not_equals';
    case Contains = 'contains';
    case NotContains = 'not_contains';
    case IsEmpty = 'is_empty';
    case IsNotEmpty = 'is_not_empty';
    case GreaterThan = 'greater_than';
    case LessThan = 'less_than';

    public function label(): string
    {
        return match ($this) {
            self::Equals => 'is equal to',
            self::NotEquals => 'is not equal to',
            self::Contains => 'contains',
            self::NotContains => 'does not contain',
            self::IsEmpty => 'is empty',
            self::IsNotEmpty => 'is not empty',
            self::GreaterThan => 'is greater than',
            self::LessThan => 'is less than',
        };
    }

    /** Whether this operator needs a "value" input (is_empty/is_not_empty don't). */
    public function needsValue(): bool
    {
        return ! in_array($this, [self::IsEmpty, self::IsNotEmpty]);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }

    /**
     * Evaluate this operator against a field's current state and a
     * target comparison value taken from the condition's config.
     */
    public function evaluate(mixed $state, mixed $target): bool
    {
        // Normalize arrays (checkbox groups) to comparable strings for contains-style checks.
        $stateIsArray = is_array($state);

        return match ($this) {
            self::Equals => $stateIsArray
                ? in_array((string) $target, array_map('strval', $state))
                : (string) $state === (string) $target,

            self::NotEquals => $stateIsArray
                ? ! in_array((string) $target, array_map('strval', $state))
                : (string) $state !== (string) $target,

            self::Contains => str_contains(
                Str::lower((string) (is_array($state) ? implode(',', $state) : $state)),
                Str::lower((string) $target)
            ),

            self::NotContains => ! str_contains(
                Str::lower((string) (is_array($state) ? implode(',', $state) : $state)),
                Str::lower((string) $target)
            ),

            self::IsEmpty => blank($state),

            self::IsNotEmpty => filled($state),

            self::GreaterThan => is_numeric($state) && is_numeric($target) && (float) $state > (float) $target,

            self::LessThan => is_numeric($state) && is_numeric($target) && (float) $state < (float) $target,
        };
    }
}
