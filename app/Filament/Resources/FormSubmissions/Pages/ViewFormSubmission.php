<?php

namespace App\Filament\Resources\FormSubmissions\Pages;

use App\Filament\Resources\FormSubmissions\FormSubmissionResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

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
                        ->flatMap(function ($value, string $label) {
                            // Handle multiple values (e.g. multiple file uploads)
                            if (is_array($value)) {
                                return collect($value)
                                    ->values()
                                    ->map(function ($item, $i) use ($label) {
                                        if (is_string($item) && (str_contains($item, '/') || str_contains($item, '.'))) {
                                            $url = Storage::url($item);

                                            return TextEntry::make($label . ' ' . ($i + 1))
                                                ->label($label)
                                                ->state(basename($item))
                                                ->url(fn() => $url)
                                                ->openUrlInNewTab();
                                        }

                                        return TextEntry::make($label . ' ' . ($i + 1))
                                            ->label($label)
                                            ->state((string) $item);
                                    })
                                    ->all();
                            }

                            // Single value - detect file path like strings and render as link
                            if (is_string($value) && (str_contains($value, '/') || str_contains($value, '.'))) {
                                $url = Storage::url($value);

                                return [TextEntry::make($label)
                                    ->label($label)
                                    ->state(basename($value))
                                    ->url(fn() => $url)
                                    ->openUrlInNewTab()];
                            }

                            return [TextEntry::make($label)
                                ->label($label)
                                ->state(is_array($value) ? implode(', ', $value) : (string) $value)];
                        })
                        ->values()
                        ->all();
                })
                ->columns(2),
        ]);
    }
}
