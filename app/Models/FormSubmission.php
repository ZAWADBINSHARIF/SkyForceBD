<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
    protected $fillable = [
        'form_id',
        'user_id',
        'data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get a human-readable label => value map, resolved against the
     * form's field definitions (so select options show their label,
     * not their raw value).
     */
    public function toLabeledArray(): array
    {
        $definitions = collect($this->form?->getFieldDefinitions() ?? [])->keyBy('name');
        $out = [];

        foreach ($this->data ?? [] as $key => $value) {
            $field = $definitions->get($key);
            $label = $field['label'] ?? $key;

            if ($field && in_array($field['type'] ?? null, ['select', 'radio', 'checkbox_group'])) {
                $options = collect($field['options'] ?? [])->pluck('label', 'value');

                $value = is_array($value)
                    ? collect($value)->map(fn($v) => $options->get($v, $v))->implode(', ')
                    : $options->get($value, $value);
            }

            $out[$label] = $value;
        }

        return $out;
    }
}
