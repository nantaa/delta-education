<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO --}}
    <title>@yield('title', config('app.name', 'Delta Education'))</title>
    <meta name="description" content="@yield('meta_description', 'Ikuti webinar terbaik bersama Delta Education dan tingkatkan skill-mu hari ini.')">
    <meta name="keywords" content="@yield('meta_keywords', 'webinar, pendidikan, online, delta education')">
    <meta name="robots" content="index, follow">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', config('app.name', 'Delta Education'))">
    <meta property="og:description" content="@yield('meta_description', 'Ikuti webinar terbaik bersama Delta Education.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Delta Education') }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Font: Instrument Sans (same as laravel.com) --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="antialiased bg-[#FDFDFC] text-[#1b1b18]" style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">

    {{-- Navigation --}}
    <header class="sticky top-0 z-50 border-b border-[#e3e3e0] bg-[#FDFDFC]/90 backdrop-blur-sm">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-14 items-center justify-between">
                <a href="{{ route('home') }}" class="text-sm font-semibold text-[#1b1b18] tracking-tight">
                    {{ config('app.name', 'Delta Education') }}
                </a>

                <nav class="hidden md:flex items-center gap-6 text-sm text-[#706f6c]">
                    <a href="{{ route('home') }}" class="hover:text-[#1b1b18] transition-colors">Beranda</a>
                    <a href="{{ route('webinars.index') }}" class="hover:text-[#1b1b18] transition-colors">Webinar</a>
                    <a href="/pelatihan" class="hover:text-[#1b1b18] transition-colors">Pelatihan K3</a>
                </nav>

                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="inline-block rounded-sm border border-[#19140035] px-4 py-1.5 text-xs font-medium text-[#1b1b18] hover:border-[#1915014a] transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-block px-4 py-1.5 text-xs font-medium text-[#706f6c] hover:text-[#1b1b18] transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('webinars.index') }}"
                           class="inline-block rounded-sm bg-[#1b1b18] px-4 py-1.5 text-xs font-medium text-white hover:bg-black transition-colors">
                            Mulai Belajar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="border-t border-[#e3e3e0] py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-[#706f6c]">
                &copy; {{ date('Y') }} {{ config('app.name', 'Delta Education') }}. Seluruh hak dilindungi.
            </p>
            <nav class="flex items-center gap-4 text-xs text-[#706f6c]">
                <a href="{{ route('webinars.index') }}" class="hover:text-[#1b1b18] transition-colors">Webinar</a>
                <a href="/pelatihan" class="hover:text-[#1b1b18] transition-colors">Pelatihan K3</a>
                <a href="{{ route('login') }}" class="hover:text-[#1b1b18] transition-colors">Masuk</a>
            </nav>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
