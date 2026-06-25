<?php

namespace App\Support\FormBuilder;

enum FieldType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case Number = 'number';
    case Email = 'email';
    case Select = 'select';
    case Radio = 'radio';
    case Checkbox = 'checkbox'; // single boolean toggle
    case CheckboxGroup = 'checkbox_group'; // multiple choice
    case Date = 'date';
    case DateTime = 'datetime';
    case FileUpload = 'file';
    case Toggle = 'toggle';
    case Heading = 'heading'; // static, non-input "heading" block
    case Section = 'section'; // static, non-input section block with title + description

    public function label(): string
    {
        return match ($this) {
            self::Text => 'Text Input',
            self::Textarea => 'Textarea',
            self::Number => 'Number',
            self::Email => 'Email',
            self::Select => 'Dropdown (Select)',
            self::Radio => 'Radio Buttons',
            self::Checkbox => 'Checkbox (Yes/No)',
            self::CheckboxGroup => 'Checkbox Group (Multiple Choice)',
            self::Date => 'Date',
            self::DateTime => 'Date & Time',
            self::FileUpload => 'File Upload',
            self::Toggle => 'Toggle Switch',
            self::Heading => 'Section Heading',
            self::Section => 'Section',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Text => 'heroicon-o-pencil',
            self::Textarea => 'heroicon-o-bars-3-bottom-left',
            self::Number => 'heroicon-o-hashtag',
            self::Email => 'heroicon-o-at-symbol',
            self::Select => 'heroicon-o-chevron-up-down',
            self::Radio => 'heroicon-o-stop-circle',
            self::Checkbox => 'heroicon-o-check-circle',
            self::CheckboxGroup => 'heroicon-o-list-bullet',
            self::Date => 'heroicon-o-calendar',
            self::DateTime => 'heroicon-o-clock',
            self::FileUpload => 'heroicon-o-paper-clip',
            self::Toggle => 'heroicon-o-power',
            self::Heading => 'heroicon-o-bars-2',
            self::Section => 'heroicon-o-rectangle-group',
        };
    }

    /** Whether this field type stores a real value (vs. being purely decorative). */
    public function isInput(): bool
    {
        return ! in_array($this, [
            self::Heading,
            self::Section,
            self::Radio,
            self::Select,
            self::Checkbox,
            self::CheckboxGroup,
            self::Date,
            self::DateTime,
            self::FileUpload,
            self::Toggle
        ]);
    }

    /** Whether this field type supports an `options` array (select/radio/checkbox group). */
    public function hasOptions(): bool
    {
        return in_array($this, [self::Select, self::Radio, self::CheckboxGroup]);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn(self $case) => [$case->value => $case->label()])
            ->all();
    }
}
