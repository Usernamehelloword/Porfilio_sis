<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>@yield('title', \App\Support\Settings::get('seo_title'))</title>
    <meta name="description" content="@yield('meta_description', \App\Support\Settings::get('meta_description'))">
    <meta name="keywords" content="{{ \App\Support\Settings::get('meta_keywords') }}">

    {{-- Open Graph --}}
    <meta property="og:title" content="@yield('og_title', \App\Support\Settings::get('seo_title'))">
    <meta property="og:description" content="@yield('og_description', \App\Support\Settings::get('meta_description'))">
    @if (\App\Support\Settings::get('og_image'))
        <meta property="og:image" content="{{ \App\Support\Content::imageUrl(\App\Support\Settings::get('og_image')) }}">
    @endif

    <link rel="icon" href="{{ \App\Support\Settings::get('favicon') ? \App\Support\Content::imageUrl(\App\Support\Settings::get('favicon')) : asset('image/logo_1.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Appearance settings managed from the admin panel (light mode only; dark mode uses the gradient system) --}}
    <style>
        :root:not([data-theme="dark"]) {
            --deep: {{ \App\Support\Settings::get('color_primary') }};
            --charcoal: {{ \App\Support\Settings::get('color_text') }};
            --terracotta: {{ \App\Support\Settings::get('color_accent') }};
            --beige: {{ \App\Support\Settings::get('color_secondary') }};
            --off-white: {{ \App\Support\Settings::get('color_background') }};
            --font-serif: '{{ \App\Support\Settings::get('font_heading') }}', Georgia, serif;
            --font-sans: '{{ \App\Support\Settings::get('font_body') }}', 'Helvetica Neue', Arial, sans-serif;
            @php $btnStyle = \App\Support\Settings::get('button_style', 'square'); @endphp
            --btn-radius: {{ $btnStyle === 'pill' ? '999px' : ($btnStyle === 'rounded' ? '10px' : '0') }};
        }

        @php $btnAnim = \App\Support\Settings::get('button_animation', 'fade'); @endphp
        @if ($btnAnim === 'fade')
        .btn { transition: opacity .3s ease; } .btn:hover { opacity: .78; }
        @elseif ($btnAnim === 'slide')
        .btn { transition: transform .3s ease; } .btn:hover { transform: translateX(6px); }
        @elseif ($btnAnim === 'scale')
        .btn { transition: transform .3s ease; } .btn:hover { transform: scale(1.05); }
        @else
        .btn { transition: none; }
        @endif
    </style>

</head>
<body>

<a class="sr-only" href="#main">Skip to content</a>

<header class="nav" id="nav">
    <div class="nav-inner">
        <a class="nav-logo" href="{{ route('home') }}">
            @if (\App\Support\Settings::get('logo'))
                <img src="{{ \App\Support\Content::imageUrl(\App\Support\Settings::get('logo')) }}" alt="{{ \App\Support\Settings::get('studio_name') }}" class="nav-logo-img">
            @else
                <img src="{{ asset('image\logo2.png') }}" alt="{{ \App\Support\Settings::get('studio_name') }}" class="nav-logo-img">
            @endif
            <span>{{ \App\Support\Settings::get('site_name') }}</span>
        </a>

        <nav aria-label="Primary">
            <ul class="nav-links">
                <li><a href="{{ route('projects.index') }}">Projects</a></li>
                <li><a href="{{ route('about') }}">About</a></li>
                <li><a href="{{ route('services') }}">Services</a></li>
                <li><a href="{{ route('journal.index') }}">Journal</a></li>
                <li><a class="nav-cta" href="{{ route('contact') }}">Contact</a></li>
            </ul>
        </nav>

        <button class="burger" type="button" aria-label="Open menu" aria-expanded="false">
            <i></i>
        </button>

        <!-- Sunrise / Sunset Theme Toggle -->
        <button class="theme-toggle" type="button" aria-label="Switch to dark mode" aria-pressed="false">
            <span class="theme-toggle__sun"></span>
            <span class="theme-toggle__horizon"></span>
            <span class="theme-toggle__label">THEME</span>
        </button>
    </div>
</header>

<div class="mobile-menu" id="mobile-menu">
    <a href="{{ route('home') }}">Home <small>01</small></a>
    <a href="{{ route('projects.index') }}">Projects <small>02</small></a>
    <a href="{{ route('about') }}">About <small>03</small></a>
    <a href="{{ route('services') }}">Services <small>04</small></a>
    <a href="{{ route('journal.index') }}">Journal <small>05</small></a>
    <a href="{{ route('contact') }}">Contact <small>06</small></a>
</div>

<main id="main">
    @yield('content')
</main>

<div class="statement wrap">
    <h2 class="display">
        <span class="line"><span>WE DESIGN SPACES</span></span>
        <span class="line"><span>THAT <em>LAST.</em></span></span>
    </h2>
</div>

<footer class="site-footer">
    <div class="wrap">
        <div class="foot-grid">
            <div>
                <span class="foot-logo">
                    @if (\App\Support\Settings::get('logo'))
                        <img src="{{ \App\Support\Content::imageUrl(\App\Support\Settings::get('logo')) }}" alt="{{ \App\Support\Settings::get('studio_name') }} logo" class="foot-logo-img">
                    @else
                        <img src="{{ asset('image/logo_2.png') }}" alt="{{ \App\Support\Settings::get('studio_name') }} logo" class="foot-logo-img">
                    @endif
                    {{ \App\Support\Settings::get('site_name') }}
                </span>
            </div>
            <div class="col-center">
                <a href="{{ route('projects.index') }}">Projects</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('services') }}">Services</a>
                <a href="{{ route('journal.index') }}">Journal</a>
            </div>
            <div class="col-right">
                @if (\App\Support\Settings::get('instagram'))<a href="{{ \App\Support\Settings::get('instagram') }}" target="_blank" rel="noopener">Instagram</a>@endif
                @if (\App\Support\Settings::get('facebook'))<a href="{{ \App\Support\Settings::get('facebook') }}" target="_blank" rel="noopener">Facebook</a>@endif
                @if (\App\Support\Settings::get('linkedin'))<a href="{{ \App\Support\Settings::get('linkedin') }}" target="_blank" rel="noopener">LinkedIn</a>@endif
                @if (\App\Support\Settings::get('youtube'))<a href="{{ \App\Support\Settings::get('youtube') }}" target="_blank" rel="noopener">YouTube</a>@endif
                <a href="mailto:{{ \App\Support\Settings::get('email') }}">Email</a>
            </div>
        </div>
        <div class="foot-bottom">
            <span>© <span data-year>2026</span> {{ \App\Support\Settings::get('studio_name') }} — Phnom Penh, Cambodia</span>
            <span>Privacy · Terms</span>
        </div>
    </div>
</footer>

<div class="cursor-dot" aria-hidden="true"></div>

</body>
</html>