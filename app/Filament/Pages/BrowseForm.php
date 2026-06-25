<?php

namespace App\Filament\Pages;

use App\Models\Form;
use App\Models\FormSubmission;
use App\Support\FormBuilder\FormSchemaBuilder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class BrowseForm extends Page
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Forms';

    protected static ?string $title = 'Forms';

    protected string $view = 'filament.pages.browse-forms';

    /**
     * Slug of the currently selected form, or null while in "browse" mode.
     */
    public ?string $activeSlug = null;

    /** @var array<string, mixed> */
    public array $data = [];

    public bool $submitted = false;

    public function mount(): void
    {
        // Support deep-linking: /admin/forms?form=contact-us opens directly into fill mode.
        $slug = request()->query('form');

        if ($slug && Form::where('slug', $slug)->where('is_active', true)->exists()) {
            $this->selectForm($slug);
        }
    }

    /**
     * All forms available to browse/fill.
     */
    public function getAvailableForms(): Collection
    {
        return Form::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->filter(fn(Form $form) => $form->isOpen());
    }

    public function getActiveForm(): ?Form
    {
        if (! $this->activeSlug) {
            return null;
        }

        return Form::where('slug', $this->activeSlug)->first();
    }

    public function selectForm(string $slug): void
    {
        $this->activeSlug = $slug;
        $this->submitted = false;
        $this->data = [];

        $this->form->fill();
    }

    public function backToList(): void
    {
        $this->activeSlug = null;
        $this->submitted = false;
        $this->data = [];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->buildFormComponents())
            ->statePath('data');
    }

    /**
     * @return array<int, Component>
     */
    protected function buildFormComponents(): array
    {
        $activeForm = $this->getActiveForm();

        if (! $activeForm) {
            return [];
        }

        return FormSchemaBuilder::build($activeForm->getFieldDefinitions());
    }

    public function submit(): void
    {
        $activeForm = $this->getActiveForm();

        if (! $activeForm) {
            return;
        }

        if (! $activeForm->isOpen()) {
            Notification::make()
                ->title('This form is no longer accepting responses.')
                ->danger()
                ->send();

            return;
        }

        $state = $this->form->getState();

        FormSubmission::create([
            'form_id' => $activeForm->id,
            'user_id' => Auth::id(),
            'data' => $state,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->submitted = true;

        Notification::make()
            ->title($activeForm->settings['success_message'] ?? 'Thanks! Your response has been recorded.')
            ->success()
            ->send();
    }
}
