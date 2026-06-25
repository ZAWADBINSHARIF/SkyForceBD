<?php

namespace App\Filament\Resources\FormSubmissions\Pages;

use App\Filament\Resources\FormSubmissions\FormSubmissionResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewFormSubmission extends ViewRecord
{
    protected static string $resource = FormSubmissionResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Submission Info')
                ->columns(3)
                ->schema([
                    TextEntry::make('form.name')->label('Form'),
                    TextEntry::make('user.name')->label('Submitted by')->placeholder('Guest'),
                    TextEntry::make('created_at')->label('Submitted at')->dateTime(),
                ]),

            Section::make('Responses')
                ->schema(function ($record) {
                    return collect($record->toLabeledArray())
                        ->map(function ($value, string $label) {
                            return TextEntry::make($label)
                                ->label($label)
                                ->state(is_array($value) ? implode(', ', $value) : (string) $value);
                        })
                        ->values()
                        ->all();
                })
                ->columns(2),
        ]);
    }
}
