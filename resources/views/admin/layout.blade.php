<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Studio Volume Admin</title>
    <link rel="icon" href="{{ \App\Support\Settings::get('favicon') ? \App\Support\Content::imageUrl(\App\Support\Settings::get('favicon')) : asset('image/logo_1.png') }}" type="image/png">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="admin-body {{ session('sidebar_collapsed') ? '' : '' }}">

<div class="admin-shell">

    <aside class="sidebar" id="admin-sidebar">
        <a class="side-brand" href="{{ route('admin.dashboard') }}">
            <span class="side-mark">SV</span>
            <span class="side-name">STUDIO<b>·</b>VOLUME <small>Admin</small></span>
        </a>

        <nav class="side-nav">
            @php
                $adminNav = [
                    ['Dashboard', 'admin.dashboard', route('admin.dashboard'), 'Overview & statistics'],
                    ['Projects', 'admin.projects.index', route('admin.projects.index'), 'Manage architecture projects'],
                    ['Categories', 'admin.categories.index', route('admin.categories.index'), 'Manage project categories'],
                    ['About', 'admin.about', route('admin.about'), 'Studio information'],
                    ['Designers', 'admin.designers.index', route('admin.designers.index'), 'Team members'],
                    ['Services', 'admin.services.index', route('admin.services.index'), 'Architectural services'],
                    ['Journal', 'admin.journal.index', route('admin.journal.index'), 'Articles & insights'],
                    ['Media', 'admin.media.index', route('admin.media.index'), 'Images & files'],
                    ['Messages', 'admin.messages.index', route('admin.messages.index'), 'Contact inquiries'],
                    ['Homepage', 'admin.homepage', route('admin.homepage'), 'Homepage sections'],
                    ['Appearance', 'admin.appearance', route('admin.appearance'), 'Colors, fonts & buttons'],
                    ['Settings', 'admin.settings', route('admin.settings'), 'General & SEO settings'],
                    ['Admin Users', 'admin.users.index', route('admin.users.index'), 'Administrators'],
                ];
            @endphp

            @foreach ($adminNav as $i => [$label, $name, $url, $hint])
                @if ($name === 'admin.appearance' || $name === 'admin.settings')
                    @if (! auth()->user()?->isSuperAdmin() && auth()->user()?->role !== 'editor')
                        @continue
                    @endif
                @endif
                @if ($name === 'admin.users.index' && ! auth()->user()?->isSuperAdmin())
                    @continue
                @endif
                <a href="{{ $url }}" class="side-link {{ request()->routeIs($name) || request()->routeIs(str_replace('.index', '.*', $name)) ? 'is-active' : '' }}">
                    <span class="side-idx">{{ sprintf('%02d', $i + 1) }}</span>
                    <span class="side-label">{{ $label }}<small>{{ $hint }}</small></span>
                </a>
            @endforeach
        </nav>

        <div class="side-foot">
            <a class="side-link" href="{{ route('home') }}" target="_blank" rel="noopener">
                <span class="side-idx">↗</span>
                <span class="side-label">View Website<small>Open the public site</small></span>
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="side-link side-logout">
                    <span class="side-idx">⇥</span>
                    <span class="side-label">Logout<small>{{ auth()->user()?->email }}</small></span>
                </button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <button type="button" class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle sidebar">☰</button>
            <h1 class="topbar-title">@yield('page_title', 'Dashboard')</h1>
            <div class="topbar-right">
                @yield('topbar_actions')
                <span class="topbar-user">{{ auth()->user()?->name }} <small>{{ \App\Models\User::ROLES[auth()->user()?->role] ?? '' }}</small></span>
            </div>
        </header>

        <div class="admin-content">
            @if (session('success'))
                <div class="flash flash-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="flash flash-error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

</body>
</html>
