<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'HelpDesk') — HelpDesk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300..900;1,14..32,300..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { font-family: 'Inter', system-ui, sans-serif; }
        [x-cloak] { display: none !important; }

        /* ─── Page shell ─────────────────────────────────────── */
        body { background: #f1f3f9; }

        /* ─── Sidebar ────────────────────────────────────────── */
        .sidebar {
            background: #0f0e2a;
            width: 56px;
            flex-shrink: 0;
            transition: width .2s ease;
            overflow: hidden;
        }
        .sidebar:hover {
            width: 220px;
        }
        .sidebar:hover .sidebar-label { opacity: 1; width: auto; }
        .sidebar-label { opacity: 0; width: 0; overflow: hidden; transition: opacity .15s, width .15s; white-space: nowrap; }
        .sidebar:hover .sidebar-logo-text { opacity: 1; width: auto; }
        .sidebar-logo-text { opacity: 0; width: 0; overflow: hidden; transition: opacity .15s; white-space: nowrap; }
        .sidebar:hover .sidebar-section-label { opacity: 1; }
        .sidebar-section-label { opacity: 0; transition: opacity .15s; }
        .sidebar:hover .sidebar-badge { opacity: 1; }
        .sidebar-badge { opacity: 0; transition: opacity .15s; }

        /* ─── Nav items ──────────────────────────────────────── */
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 12px; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            color: #8b8fad;
            text-decoration: none;
            transition: background .12s, color .12s;
            cursor: pointer;
        }
        .nav-item:hover { background: rgba(255,255,255,.06); color: #c5c8e8; }
        .nav-item.active {
            background: rgba(99,102,241,.18);
            color: #a5b4fc;
            font-weight: 600;
        }
        .nav-item .ni { width: 15px; height: 15px; flex-shrink: 0; opacity: .7; }
        .nav-item.active .ni { opacity: 1; }

        /* ─── Topbar ─────────────────────────────────────────── */
        .topbar {
            height: 52px;
            background: #fff;
            border-bottom: 1px solid #e8eaf2;
            display: flex; align-items: center;
            padding: 0 20px;
            gap: 12px;
            flex-shrink: 0;
        }

        /* ─── Cards ──────────────────────────────────────────── */
        .card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e8eaf2;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
        }
        .card-lift { transition: box-shadow .18s, transform .18s; }
        .card-lift:hover { box-shadow: 0 6px 24px rgba(79,70,229,.1); transform: translateY(-2px); }

        /* ─── Buttons ────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 8px;
            font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all .12s;
            border: none; text-decoration: none; white-space: nowrap;
            line-height: 1.4;
        }
        .btn-primary {
            background: #4f46e5; color: #fff;
            box-shadow: 0 1px 3px rgba(79,70,229,.4);
        }
        .btn-primary:hover { background: #4338ca; color: #fff; box-shadow: 0 3px 10px rgba(79,70,229,.4); }
        .btn-secondary { background: #fff; color: #374151; border: 1px solid #d1d5db; }
        .btn-secondary:hover { background: #f9fafb; border-color: #a5b4fc; color: #4f46e5; }
        .btn-danger { background: #fff; color: #dc2626; border: 1px solid #fca5a5; }
        .btn-danger:hover { background: #fef2f2; }
        .btn-sm { padding: 5px 10px; font-size: 12px; border-radius: 6px; }
        .btn-xs { padding: 3px 8px; font-size: 11px; border-radius: 5px; }

        /* ─── Inputs ─────────────────────────────────────────── */
        .input {
            width: 100%; border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 8px 12px; font-size: 13px;
            outline: none; background: #fff; color: #111827;
            transition: border-color .12s, box-shadow .12s;
            font-family: inherit;
        }
        .input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
        select.input { cursor: pointer; }

        /* ─── Empty state ────────────────────────────────────── */
        .empty-state {
            background: #fff; border-radius: 16px;
            border: 2px dashed #c7d2fe;
            padding: 64px 32px; text-align: center;
        }

        /* ─── Badges ─────────────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 2px 8px; border-radius: 99px;
            font-size: 11px; font-weight: 600;
        }

        /* ─── Animations ─────────────────────────────────────── */
        @keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .fade-up { animation: fadeUp .25s ease both; }

        /* ─── Scrollbar ──────────────────────────────────────── */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c7d2fe; border-radius: 99px; }
    </style>
    @stack('head')
</head>
<body class="h-full antialiased">

@auth
@php
    $user      = auth()->user();
    $openCount = \App\Models\Ticket::where('status', \App\Enums\TicketStatus::Open)->count();
    $navItems  = $user->isStaff() ? [
        ['label'=>'Dashboard',      'icon'=>'grid',   'route'=>'admin.dashboard',            'active'=>'admin.dashboard'],
        ['label'=>'Tickets',        'icon'=>'ticket', 'route'=>'admin.tickets.index',        'active'=>'admin.tickets.*', 'badge'=>$openCount],
        ['label'=>'Users',          'icon'=>'users',  'route'=>'admin.users.index',          'active'=>'admin.users.*'],
        ['label'=>'Categories',     'icon'=>'tag',    'route'=>'admin.categories.index',     'active'=>'admin.categories.*'],
        ['label'=>'Templates',      'icon'=>'file',   'route'=>'admin.templates.index',      'active'=>'admin.templates.*'],
        ['label'=>'Knowledge Base', 'icon'=>'book',   'route'=>'admin.knowledge-base.index', 'active'=>'admin.knowledge-base.*'],
        ['label'=>'Reports',        'icon'=>'chart',  'route'=>'admin.reports.index',        'active'=>'admin.reports.*'],
        ['label'=>'Audit Logs',     'icon'=>'log',    'route'=>'admin.audit-logs.index',     'active'=>'admin.audit-logs.*'],
    ] : [
        ['label'=>'Dashboard',      'icon'=>'grid',   'route'=>'student.dashboard',          'active'=>'student.dashboard'],
        ['label'=>'My Tickets',     'icon'=>'ticket', 'route'=>'student.tickets.index',      'active'=>'student.tickets.index'],
        ['label'=>'New Ticket',     'icon'=>'plus',   'route'=>'student.tickets.create',     'active'=>'student.tickets.create'],
        ['label'=>'Knowledge Base', 'icon'=>'book',   'route'=>'knowledge-base.index',       'active'=>'knowledge-base.*'],
    ];
@endphp

<div class="flex h-full overflow-hidden">

    {{-- ══ SIDEBAR ══════════════════════════════════════════════════════════ --}}
    <aside class="sidebar hidden lg:flex flex-col h-full overflow-hidden">

        {{-- Logo --}}
        <div class="flex items-center gap-2.5 px-3 py-4" style="border-bottom:1px solid rgba(255,255,255,.06);">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div class="sidebar-logo-text">
                <p class="text-sm font-bold text-white leading-none tracking-tight">HelpDesk</p>
                <p class="text-[10px] mt-0.5" style="color:#5b5f7e;">IT Support Portal</p>
            </div>
        </div>

        {{-- Section label --}}
        <div class="px-3 pt-5 pb-1">
            <p class="sidebar-section-label text-[10px] font-semibold uppercase tracking-widest" style="color:#3d4166;">
                {{ $user->isStaff() ? 'Staff' : 'Student' }}
            </p>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-2 pb-2 space-y-0.5 overflow-y-auto">
            @foreach ($navItems as $item)
                @php $isActive = request()->routeIs($item['active']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="nav-item {{ $isActive ? 'active' : '' }}"
                   title="{{ $item['label'] }}">
                    @switch($item['icon'])
                        @case('grid')<svg class="ni" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>@break
                        @case('ticket')<svg class="ni" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>@break
                        @case('users')<svg class="ni" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>@break
                        @case('tag')<svg class="ni" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>@break
                        @case('file')<svg class="ni" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>@break
                        @case('book')<svg class="ni" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>@break
                        @case('chart')<svg class="ni" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>@break
                        @case('log')<svg class="ni" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>@break
                        @case('plus')<svg class="ni" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>@break
                    @endswitch
                    <span class="sidebar-label flex-1 truncate">{{ $item['label'] }}</span>
                    @if (!empty($item['badge']) && $item['badge'] > 0)
                        <span class="sidebar-badge text-[10px] font-bold px-1.5 py-0.5 rounded-full shrink-0" style="background:#ef4444;color:#fff;min-width:18px;text-align:center;line-height:1.4;">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="p-2" style="border-top:1px solid rgba(255,255,255,.06);">
            <div class="flex items-center gap-2.5 rounded-lg px-2 py-2.5" style="background:rgba(255,255,255,.04);">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="sidebar-label min-w-0 flex-1">
                    <p class="text-xs font-semibold text-white truncate leading-none">{{ $user->name }}</p>
                    <p class="text-[10px] mt-0.5 truncate" style="color:#5b5f7e;">{{ $user->role->label() }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="sidebar-label shrink-0">
                    @csrf
                    <button type="submit" title="Sign out" class="transition-colors" style="color:#5b5f7e;" onmouseover="this.style.color='#a5b4fc'" onmouseout="this.style.color='#5b5f7e'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ══ MAIN AREA ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        {{-- Topbar --}}
        <header class="topbar">
            {{-- Page title --}}
            <div class="flex-1 min-w-0">
                <h1 class="text-sm font-bold text-gray-900 truncate">@yield('page_title', 'Dashboard')</h1>
                @hasSection('page_subtitle')
                    <p class="text-xs text-gray-400 truncate">@yield('page_subtitle')</p>
                @endif
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-2 shrink-0">
                {{-- Mobile nav --}}
                <nav class="flex flex-wrap items-center gap-1 lg:hidden">
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}" class="rounded px-2 py-1 text-xs font-semibold {{ request()->routeIs($item['active']) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                {{-- User chip --}}
                <div class="hidden lg:flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <span class="text-xs font-semibold text-gray-700">{{ $user->name }}</span>
                    <span class="text-xs text-gray-400">·</span>
                    <span class="text-xs text-gray-400">{{ $user->role->label() }}</span>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-auto p-5">
            @if (session('status'))
                <div class="mb-4 flex items-center gap-2.5 rounded-xl px-4 py-3 text-sm font-medium fade-up" style="background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 flex items-start gap-2.5 rounded-xl px-4 py-3 text-sm fade-up" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <ul class="space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

@else
{{-- ══ GUEST LAYOUT ════════════════════════════════════════════════════════ --}}
<div class="min-h-full flex flex-col" style="background:linear-gradient(135deg,#f0f2ff 0%,#faf5ff 100%);">
    <header class="sticky top-0 z-10" style="background:rgba(255,255,255,.9);backdrop-filter:blur(12px);border-bottom:1px solid #e8eaf2;">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-3.5">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-900 tracking-tight">HelpDesk</span>
            </a>
            <nav class="flex items-center gap-1.5">
                <a href="{{ route('knowledge-base.index') }}" class="hidden sm:inline-flex rounded-lg px-3 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 transition">Knowledge Base</a>
                <a href="{{ route('login') }}" class="rounded-lg px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
            </nav>
        </div>
    </header>
    <main class="mx-auto w-full max-w-6xl flex-1 px-5 py-10">@yield('content')</main>
    <footer class="border-t border-gray-200 bg-white py-5 text-center text-xs text-gray-400">
        © {{ date('Y') }} Student IT Help Desk · All rights reserved
    </footer>
</div>
@endauth
</body>
</html>
