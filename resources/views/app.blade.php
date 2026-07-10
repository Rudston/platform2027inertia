<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ config('app.name', 'Platform2027') }}</title>

    {{-- Apply saved/system theme before paint to avoid a flash. Dark mode is a
         `.dark` class on <html> (same mechanism as the Livewire pages). --}}
    <script>
        (function () {
            const stored = localStorage.getItem('theme');
            const dark = stored === 'dark'
                || (! stored && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (dark) document.documentElement.classList.add('dark');
        })();
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>
<body class="min-h-screen bg-surface text-main antialiased">
    @inertia
</body>
</html>
