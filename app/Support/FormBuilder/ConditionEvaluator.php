<?php

namespace App\Support\FormBuilder;

class ConditionEvaluator
{
    /**
     * A field's "conditions" config looks like:
     *
     * [
     *   'enabled' => true,
     *   'logic' => 'all', // 'all' = AND, 'any' = OR
     *   'action' => 'show', // 'show' or 'require'
     *   'rules' => [
     *       ['field' => 'has_pet', 'operator' => 'equals', 'value' => 'yes'],
     *       ['field' => 'pet_count', 'operator' => 'greater_than', 'value' => '1'],
     *   ],
     * ]
     *
     * $state is the full live form data (Filament's $get('../../') style
     * flat array, e.g. ['has_pet' => 'yes', 'pet_count' => 3, ...]).
     */
    public static function passes(?array $conditions, array $state): bool
    {
        if (empty($conditions) || ! ($conditions['enabled'] ?? false)) {
            // No conditions configured -> field is always visible/active.
            return true;
        }

        $rules = $conditions['rules'] ?? [];

        if (empty($rules)) {
            return true;
        }

        $logic = $conditions['logic'] ?? 'all';

        $results = collect($rules)->map(function (array $rule) use ($state) {
            $operator = ConditionOperator::tryFrom($rule['operator'] ?? '');

            if (! $operator) {
                return true;
            }

            $fieldState = $state[$rule['field'] ?? ''] ?? null;

            return $operator->evaluate($fieldState, $rule['value'] ?? null);
        });

        return $logic === 'any'
            ? $results->contains(true)
            : $results->every(fn ($r) => $r === true);
    }

    /**
     * Convenience helper: should this field be required, given its base
     * "required" flag plus any conditional "require" rules?
     */
    public static function isRequired(array $fieldDefinition, array $state): bool
    {
        $conditions = $fieldDefinition['conditions'] ?? null;

        // If conditions exist and their action is "require", base requirement
        // is overridden by whether the condition passes.
        if ($conditions && ($conditions['enabled'] ?? false) && ($conditions['action'] ?? 'show') === 'require') {
            return static::passes($conditions, $state);
        }

        return (bool) ($fieldDefinition['required'] ?? false);
    }

    /**
     * Convenience helper: should this field currently be visible?
     */
    public static function isVisible(array $fieldDefinition, array $state): bool
    {
        $conditions = $fieldDefinition['conditions'] ?? null;

        if ($conditions && ($conditions['enabled'] ?? false) && ($conditions['action'] ?? 'show') === 'show') {
            return static::passes($conditions, $state);
        }

        // If the condition's action is "require" rather than "show", the field
        // stays visible regardless; only its required-ness is conditional.
        return true;
    }
}
