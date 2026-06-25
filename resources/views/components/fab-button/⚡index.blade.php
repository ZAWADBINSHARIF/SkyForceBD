<?php

use App\Models\Contact;
use Livewire\Component;

new class extends Component
{
    public ?Contact $contact = null;

    public function mount(): void
    {
        $this->contact = Contact::first();
    }
};
?>

<span class="fixed md:hidden bottom-20 right-5 z-10">
    <a href="tel:{{$contact->phone?? '09678771880'}}"
        class="w-14 h-14 flex justify-center items-center bg-primary-500 rounded-full shadow-lg hover:bg-primary-700 hover:scale-110 active:scale-95 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-offset-2">
        <x-heroicon-s-phone class="text-white w-6 h-6" />
    </a>
</span>