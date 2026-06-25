<?php

namespace App\Filament\Resources\Forms\Pages;

use App\Filament\Resources\Forms\FormResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewForm extends ViewRecord
{
    protected static string $resource = FormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Preview public form')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn() => route('forms.show', $this->record->slug))
                ->openUrlInNewTab(),

            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Overview')
                ->columns(3)
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('slug')->badge(),
                    TextEntry::make('is_active')->label('Active')->badge()
                        ->formatStateUsing(fn(bool $state) => $state ? 'Yes' : 'No')
                        ->color(fn(bool $state) => $state ? 'success' : 'danger'),
                    TextEntry::make('submissions_count')
                        ->label('Total submissions')
                        ->state(fn($record) => $record->submissions()->count()),
                    TextEntry::make('created_at')->dateTime(),
                ]),
        ]);
    }
}
