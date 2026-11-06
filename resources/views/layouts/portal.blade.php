<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>@yield('title', 'Barangay Estanza') · Barangay Estanza</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal-body">
<div class="portal-shell">
    <aside class="portal-sidebar">
        <a class="brand-lockup" href="{{ route('dashboard') }}"><span class="brand-seal" aria-hidden="true"><svg viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="21" stroke="currentColor" stroke-width="1.5"/><path d="M10 31h28M14 30V21l10-7 10 7v9M20 30v-7h8v7M16 19l8-5 8 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 35c7-3 17-3 24 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span><span><strong>Barangay Estanza</strong><small>Community services portal</small></span></a>
        <p class="nav-caption">{{ auth()->user()->hasRole('user') ? 'MY ACCOUNT' : 'WORKSPACE' }}</p>
        <nav class="portal-nav" aria-label="Main navigation">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span>▦</span><span>Dashboard</span></a>
            @if (auth()->user()->hasRole('user'))
                <a class="nav-link {{ request()->routeIs('my.certificates.create') ? 'active' : '' }}" href="{{ route('my.certificates.create') }}"><span>▤</span><span>Request certificate</span></a>
                <a class="nav-link {{ request()->routeIs('my.certificates.index') ? 'active' : '' }}" href="{{ route('my.certificates.index') }}"><span>☷</span><span>My certificate requests</span></a>
                <a class="nav-link {{ request()->routeIs('my.problems.create') ? 'active' : '' }}" href="{{ route('my.problems.create') }}"><span>!</span><span>Report a problem</span></a>
                <a class="nav-link {{ request()->routeIs('my.problems.index') ? 'active' : '' }}" href="{{ route('my.problems.index') }}"><span>◷</span><span>My problem reports</span></a>
            @else
                <a class="nav-link {{ request()->routeIs('residents.*') ? 'active' : '' }}" href="{{ route('residents.index') }}"><span>♙</span><span>Residents</span></a>
                <a class="nav-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}" href="{{ route('certificates.index') }}"><span>▤</span><span>Certificates</span></a>
                <a class="nav-link {{ request()->routeIs('requests.*') ? 'active' : '' }}" href="{{ route('requests.index') }}"><span>☷</span><span>Requests</span></a>
                <a class="nav-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}" href="{{ route('announcements.index') }}"><span>◂</span><span>Announcements</span></a>
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><span>▥</span><span>Reports</span></a>
                <a class="nav-link {{ request()->routeIs('problem-reports.*') ? 'active' : '' }}" href="{{ route('problem-reports.index') }}"><span>!</span><span>Problem reports</span></a>
            @endif
            @if (auth()->user()->hasRole('admin'))
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><span>♧</span><span>User accounts</span></a>
            @endif
        </nav>
        <div class="sidebar-bottom">
            <a class="nav-link settings-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.edit') }}"><span>⚙</span><span>Settings</span></a>
            <div class="user-panel"><span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span class="user-meta"><strong>{{ auth()->user()->name }}</strong><small>{{ ucfirst(auth()->user()->role) }} account</small></span><button class="signout-button" type="button" aria-label="Sign out" title="Sign out" data-logout-open>↗</button></div>
        </div>
    </aside>
    <main class="portal-main">
        <header class="topbar"><div class="mobile-brand"><span class="brand-seal small-seal">BE</span><strong>Barangay Estanza</strong></div><div class="topbar-spacer"></div>@if (auth()->user()->hasRole('staff', 'admin'))<form class="search-box" method="GET" action="{{ route('residents.index') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" name="q" placeholder="Search residents" aria-label="Search residents"></form>@endif<span class="avatar top-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span></header>
        <div class="dashboard-content">
            @if (session('status'))<div class="notice-success" role="status">{{ session('status') }}</div>@endif
            @if ($errors->any())<div class="notice-error" role="alert"><strong>A few details need another look.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
            <footer class="dashboard-footer"><span>Barangay Estanza · Community services portal</span><span>{{ ucfirst(auth()->user()->role) }} workspace</span></footer>
        </div>
    </main>
</div>
<dialog class="logout-dialog" id="logout-dialog" aria-labelledby="logout-dialog-title">
    <div class="logout-dialog-card">
        <span class="logout-dialog-mark" aria-hidden="true">↗</span>
        <h2 id="logout-dialog-title">Sign out?</h2>
        <p>Are you sure you want to sign out of your account?</p>
        <div class="logout-dialog-actions">
            <button class="secondary-action" type="button" data-logout-close>Stay signed in</button>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="primary-action" type="submit">Sign out</button></form>
        </div>
    </div>
</dialog>
</body>
</html>
