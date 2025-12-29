<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light"> {{-- 'light' pour le fond blanc --}}
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        {{-- On appelle le composant sidebar que l'on vient de créer --}}
        <x-layouts.app.sidebar /> 

        {{-- Header pour le mobile --}}
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:spacer />
        </flux:header>

        {{-- C'est ici que s'affiche tes pages (le dashboard, etc.) --}}
        <flux:main>
            {{ $slot }}
        </flux:main>

        @fluxScripts
    </body>
</html>