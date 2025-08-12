<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-100/60 dark:bg-slate-900" x-data x-init="if(localStorage.getItem('pp_dark')==='1'){document.documentElement.classList.add('dark')}">
        @include('components.nav')
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-10 sm:pt-16">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white/80 dark:bg-slate-800/80 backdrop-blur shadow-lg overflow-hidden sm:rounded-2xl ring-1 ring-slate-200 dark:ring-slate-700 animate-fade-in">
                {{ $slot }}
            </div>
        </div>
        @include('components.footer')
    </body>
</html>
