<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name')) — HelpDesk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full bg-slate-100 text-slate-900 antialiased">
    @auth
        @php
            $user = auth()->user();
            $navItems = $user->isStaff()
                ? [
                    ['label' => 'Dashboard',     'icon' => 'grid',   'route' => 'admin.dashboard',            'active' => 'admin.dashboard'],
                    ['label' => 'Tickets',        'icon' => 'ticket', 'route' => 'admin.tickets.index',        'active' => 'admin.tickets.*', 'badge' => \App\Models\Ticket::where('status', \App\Enums\TicketStatus::Open)->count()],
                    ['label' => 'Templates',      'icon' => 'file',   'route' => 'admin.templates.index',      'active' => 'admin.templates.*'],
                    ['label' => 'Knowledge Base', 'icon' => 'book',   'route' => 'admin.knowledge-base.index', 'active' => 'admin.knowledge-base.*'],
                    ['label' => 'Reports',        'icon' => 'chart',  'route' => 'admin.reports.index',        'active' => 'admin.reports.*'],
                    ['label' => 'Users',          'icon' => 'users',  'route' => 'admin.users.index',          'active' => 'admin.users.*'],
                    ['label' => 'Audit Logs',     'icon' => 'log',    'route' => 'admin.audit-logs.index',     'active' => 'admin.audit-logs.*'],
                ]
                : [
                    ['label' => 'Dashboard',  'icon' => 'grid',   'route' => 'student.dashboard',        'active' => 'student.dashboard'],
                    ['label' => 'My Tickets', 'icon' => 'ticket', 'route' => 'student.tickets.index',    'active' => 'student.tickets.index'],
                    ['label' => 'New Ticket', 'icon' => 'plus',   'route' => 'student.tickets.create',   'active' => 'student.tickets.create'],
                    ['label' => 'Knowledge Base','icon'=>'book',  'route' => 'knowledge-base.index',     'active' => 'knowledge-base.*'],
                ];
        @endphp

        <div class="flex h-full">
            {{-- Sidebar --}}
            <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-gradient-to-b from-indigo-900 to-indigo-950 text-white shadow-2xl">
                <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
                    <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/15 shadow-inner">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-indigo-300">IT Support</p>
                        <p class="text-base font-bold leading-tight">HelpDesk</p>
                    </div>
                </div>

                <nav class="flex-1 px-3 py-4 space-y-1">
                    @foreach ($navItems as $item)
                        @php $isActive = request()->routeIs($item['active']); @endphp
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-150
                                  {{ $isActive ? 'bg-white text-indigo-900 shadow-md' : 'text-indigo-200 hover:bg-white/10 hover:text-white' }}">
                            @if ($item['icon'] === 'grid')
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            @elseif ($item['icon'] === 'ticket')
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            @elseif ($item['icon'] === 'file')
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @elseif ($item['icon'] === 'chart')
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            @elseif ($item['icon'] === 'plus')
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            @elseif ($item['icon'] === 'book')
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            @elseif ($item['icon'] === 'users')
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            @elseif ($item['icon'] === 'log')
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            @endif
                            {{ $item['label'] }}
                            @if (!empty($item['badge']) && $item['badge'] > 0)
                                <span class="ml-auto rounded-full bg-rose-500 px-1.5 py-0.5 text-xs font-bold text-white leading-none">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </nav>

                <div class="border-t border-white/10 p-4">
                    <div class="flex items-center gap-3 rounded-xl bg-white/10 px-3 py-3">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-400 text-indigo-900 font-bold text-sm shrink-0">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-white">{{ $user->name }}</p>
                            <p class="text-xs text-indigo-300">{{ $user->role->label() }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-xs font-semibold text-indigo-300 hover:bg-white/10 hover:text-white transition text-left">
                            Sign out
                        </button>
                    </form>
                </div>
            </aside>

            {{-- Main content --}}
            <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
                {{-- Top bar --}}
                <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-4 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">{{ $user->isStaff() ? 'Staff workspace' : 'Student workspace' }}</p>
                            <h1 class="text-xl font-bold text-slate-900 leading-tight">@yield('page_title', 'Dashboard')</h1>
                        </div>
                        {{-- Mobile nav --}}
                        <nav class="flex flex-wrap items-center gap-1 lg:hidden">
                            @foreach ($navItems as $item)
                                <a href="{{ route($item['route']) }}"
                                   class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ request()->routeIs($item['active']) ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200">Sign out</button>
                            </form>
                        </nav>
                    </div>
                </header>

                <main class="flex-1 overflow-auto p-4 sm:p-6">
                    @if (session('status'))
                        <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
                            <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
                            <svg class="w-4 h-4 shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <ul class="space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>

    @else
        {{-- Guest layout --}}
        <div class="min-h-full flex flex-col bg-gradient-to-br from-slate-50 to-indigo-50">
            <header class="bg-white/80 backdrop-blur border-b border-slate-200 shadow-sm sticky top-0 z-10">
                <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-600 shadow">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <span class="text-base font-bold text-slate-900 tracking-tight">HelpDesk</span>
                    </a>
                    <nav class="flex items-center gap-2">
                        <a href="{{ route('knowledge-base.index') }}" class="hidden sm:inline-flex rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900">Knowledge Base</a>
                        <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Log in</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Register</a>
                    </nav>
                </div>
            </header>

            <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10">
                @yield('content')
            </main>

            <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-400">
                © {{ date('Y') }} Student IT Help Desk · All rights reserved
            </footer>
        </div>
    @endauth
</body>
</html>
