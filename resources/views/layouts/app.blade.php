<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Risk Mitigate') · {{ config('app.name', 'Risk Mitigate') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('head')
</head>
<body>
@auth
    <div class="rm-shell">
        <aside class="rm-sidebar">
            <div class="rm-brand">
                <span class="rm-logo">RM</span>
                <span>Risk&nbsp;Mitigate</span>
            </div>
            <nav class="rm-nav">
                <div class="nav-section">Workspace</div>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'active' : '' }}">
                    <span class="ico">▦</span> Dashboard
                </a>
                <a href="{{ route('projects.create') }}" class="{{ request()->routeIs('projects.create') ? 'active' : '' }}">
                    <span class="ico">＋</span> Daftarkan Proyek
                </a>
                <a href="{{ route('projects.search') }}" class="{{ request()->routeIs('projects.search') ? 'active' : '' }}">
                    <span class="ico">⌕</span> Cari Proyek
                </a>
                <div class="nav-section">Akun</div>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('sidebar-logout').submit();">
                    <span class="ico">⎋</span> Logout
                </a>
                <form id="sidebar-logout" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </nav>
        </aside>

        <div class="rm-main">
            <header class="rm-topbar">
                <div class="text-muted small">
                    Sistem Pendukung Keputusan · Mitigasi Risiko Proyek TI &amp; R&amp;D
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <span class="rank-chip" style="background:#1c4066">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        <span class="fw-semibold text-dark">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text small text-muted">{{ Auth::user()->email }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                        </li>
                    </ul>
                </div>
            </header>

            <main class="rm-content">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    </div>
@else
    <div class="rm-auth-wrap">
        @yield('content')
    </div>
@endauth
</body>
</html>
