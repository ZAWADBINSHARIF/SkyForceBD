<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-gray-50 min-h-screen font-sans text-gray-900"">

    <livewire:navbar />

    <livewire:auth.auth-modal />

    <livewire:commons.modals.confirmation-modal />

    <livewire:profile.modal/>

    <main class=" container mx-auto px-4 md:px-6">

    {{ $slot }}

    </main>

    <livewire:footer />

    <livewire:tab-button-nav />

    <livewire:fab-button />

    @livewireScripts
</body>

</html>