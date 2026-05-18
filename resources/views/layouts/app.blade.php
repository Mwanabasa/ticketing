<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name')) — HelpDesk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        *{font-family:'Inter',system-ui,sans-serif;}
        [x-cloak]{display:none!important}

        /* ── Page background ── */
        .page-bg{background:#f4f5fb;}

        /* ── Top bar ── */
        .topbar{
            background:rgba(255,255,255,0.95);
            backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);
            border-bottom:1px solid rgba(99,102,241,0.08);
            box-shadow:0 1px 0 rgba(0,0,0,0.04),0 2px 8px rgba(99,102,241,0.04);
        }

        /* ── Cards ── */
        .card{background:#fff;border-radius:16px;border:1px solid rgba(0,0,0,0.06);box-shadow:0 1px 2px rgba(0,0,0,0.04),0 4px 12px rgba(0,0,0,0.03);}
        .card:hover{box-shadow:0 4px 24px rgba(99,102,241,0.1);border-color:rgba(99,102,241,0.15);}

        /* ── Buttons ── */
        .btn{display:inline-flex;align-items:center;gap:6px;border-radius:10px;padding:9px 16px;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s ease;border:none;text-decoration:none;}
        .btn-primary{background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;box-shadow:0 2px 8px rgba(79,70,229,0.3);}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 16px rgba(79,70,229,0.4);color:#fff;}
        .btn-secondary{background:#fff;color:#374151;border:1.5px solid #e5e7eb;}
        .btn-secondary:hover{background:#f9fafb;border-color:#c7d2fe;color:#4f46e5;}
        .btn-danger{background:#fff;color:#dc2626;border:1.5px solid #fecaca;}
        .btn-danger:hover{background:#fef2f2;border-color:#f87171;}

        /* ── Inputs ── */
        .input{width:100%;border-radius:10px;border:1.5px solid #e5e7eb;padding:9px 13px;font-size:13px;outline:none;transition:border-color .15s,box-shadow .15s;background:#fff;color:#111827;}
        .input:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,0.1);}
        select.input{cursor:pointer;}

        /* ── Page header bar ── */
        .page-header{background:#fff;border-radius:16px;border:1px solid rgba(0,0,0,0.06);padding:20px 24px;margin-bottom:20px;box-shadow:0 1px 2px rgba(0,0,0,0.04);}

        /* ── Empty state ── */
        .empty-state{background:#fff;border-radius:20px;border:2px dashed #e0e7ff;padding:64px 32px;text-align:center;}

        /* ── Section title ── */
        .section-title{font-size:15px;font-weight:700;color:#111827;}
        .section-sub{font-size:12px;color:#9ca3af;margin-top:2px;}

        /* ── Animations ── */
        @keyframes fadeUp{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-up{animation:fadeUp .25s ease forwards;}

        /* ── Scrollbar ── */
        ::-webkit-scrollbar{width:5px;height:5px}
        ::-webkit-scrollbar-track{background:transparent}
        ::-webkit-scrollbar-thumb{background:#ddd6fe;border-radius:99px}
        ::-webkit-scrollbar-thumb:hover{background:#a5b4fc}
    </style>
    @stack('head')
</head>
<body class="h-full antialiased" style="background:#f4f5fb;">
@auth
@php
    $user = auth()->user();
    $navItems = $user->isStaff()
        ? [
            ['label'=>'Dashboard',    'icon'=>'grid',  'route'=>'admin.dashboard',           'active'=>'admin.dashboard'],
            ['label'=>'Tickets',      'icon'=>'ticket','route'=>'admin.tickets.index',        'active'=>'admin.tickets.*',
             'badge'=>\Illuminate\Support\Facades\Cache::remember('open_tickets',60,fn()=>\App\Models\Ticket::where('status',\App\Enums\TicketStatus::Open)->count())],
            ['label'=>'Templates',    'icon'=>'file',  'route'=>'admin.templates.index',      'active'=>'admin.templates.*'],
            ['label'=>'Knowledge Base','icon'=>'book', 'route'=>'admin.knowledge-base.index', 'active'=>'admin.knowledge-base.*'],
            ['label'=>'Reports',      'icon'=>'chart', 'route'=>'admin.reports.index',        'active'=>'admin.reports.*'],
            ['label'=>'Categories',   'icon'=>'tag',   'route'=>'admin.categories.index',     'active'=>'admin.categories.*'],
            ['label'=>'Users',        'icon'=>'users', 'route'=>'admin.users.index',          'active'=>'admin.users.*'],
            ['label'=>'Audit Logs',   'icon'=>'log',   'route'=>'admin.audit-logs.index',     'active'=>'admin.audit-logs.*'],
        ]
        : [
            ['label'=>'Dashboard',    'icon'=>'grid',  'route'=>'student.dashboard',      'active'=>'student.dashboard'],
            ['label'=>'My Tickets',   'icon'=>'ticket','route'=>'student.tickets.index',  'active'=>'student.tickets.index',
             'badge'=>\App\Models\Ticket::where('user_id',$user->id)->whereIn('status',[\App\Enums\TicketStatus::Open,\App\Enums\TicketStatus::Pending])->count()],
            ['label'=>'New Ticket',   'icon'=>'plus',  'route'=>'student.tickets.create', 'active'=>'student.tickets.create'],
            ['label'=>'Knowledge Base','icon'=>'book', 'route'=>'knowledge-base.index',   'active'=>'knowledge-base.*'],
        ];
    $unreadCount = $user->appNotifications()->whereNull('read_at')->count();
    $recentNotifs = $user->appNotifications()->limit(6)->get();
@endphp
<div class="flex h-full">
{{-- ── SIDEBAR ─────────────────────────────────────────────────────────── --}}
<aside class="hidden lg:flex lg:flex-col w-60 shrink-0 relative overflow-hidden"
       style="background:linear-gradient(180deg,#0d0b2e 0%,#13104a 40%,#1a1660 70%,#1e1a6e 100%);box-shadow:2px 0 24px rgba(0,0,0,0.35);">
    <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(ellipse at 50% -10%,rgba(99,102,241,0.2) 0%,transparent 60%);"></div>
    {{-- Logo --}}
    <div class="relative flex items-center gap-3 px-5 py-5" style="border-bottom:1px solid rgba(255,255,255,0.06);">
        <div class="flex items-center justify-center w-9 h-9 rounded-xl shrink-0"
             style="background:linear-gradient(135deg,#6366f1,#8b5cf6);box-shadow:0 4px 14px rgba(99,102,241,0.5);">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-white font-bold text-sm leading-none tracking-tight">HelpDesk</p>
            <p class="text-indigo-400 text-[11px] mt-0.5 font-medium">IT Support Portal</p>
        </div>
    </div>
    {{-- Section label --}}
    <div class="px-5 pt-5 pb-1.5">
        <p class="text-[10px] font-bold uppercase tracking-[0.18em]" style="color:rgba(148,163,184,0.5);">
            {{ $user->isStaff() ? 'Staff Panel' : 'Student Panel' }}
        </p>
    </div>
    {{-- Nav --}}
    <nav class="relative flex-1 px-3 space-y-0.5 pb-3 overflow-y-auto">
        @foreach ($navItems as $item)
            @php $isActive = request()->routeIs($item['active']); @endphp
            <a href="{{ route($item['route']) }}"
               class="group relative flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-[13px] font-semibold transition-all duration-150
                      {{ $isActive ? 'text-indigo-900' : 'text-indigo-300 hover:text-white' }}">
                @if($isActive)
                    <span class="absolute inset-0 rounded-xl" style="background:linear-gradient(135deg,rgba(255,255,255,0.96),rgba(238,242,255,0.94));box-shadow:0 2px 12px rgba(99,102,241,0.2);"></span>
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full" style="background:linear-gradient(180deg,#6366f1,#8b5cf6);"></span>
                @else
                    <span class="absolute inset-0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity" style="background:rgba(255,255,255,0.05);"></span>
                @endif
                <span class="relative flex items-center justify-center w-7 h-7 rounded-lg shrink-0 {{ $isActive ? 'bg-indigo-100 text-indigo-700' : 'text-indigo-400 group-hover:text-white' }}">
                    @switch($item['icon'])
                        @case('grid')<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>@break
                        @case('ticket')<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>@break
                        @case('file')<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>@break
                        @case('book')<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>@break
                        @case('chart')<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>@break
                        @case('plus')<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>@break
                        @case('users')<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>@break
                        @case('tag')<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>@break
                        @case('log')<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>@break
                    @endswitch
                </span>
                <span class="relative flex-1">{{ $item['label'] }}</span>
                @if(!empty($item['badge']) && $item['badge'] > 0)
                    <span class="relative rounded-full text-[10px] font-bold px-1.5 py-0.5 leading-none {{ $isActive ? 'bg-indigo-600 text-white' : 'bg-red-500 text-white' }}">{{ $item['badge'] }}</span>
                @endif
            </a>
        @endforeach
    </nav>
    {{-- User footer --}}
    <div class="relative p-3" style="border-top:1px solid rgba(255,255,255,0.06);">
        <div class="flex items-center gap-2.5 rounded-xl px-3 py-2.5" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                 style="background:linear-gradient(135deg,#6366f1,#8b5cf6);box-shadow:0 2px 8px rgba(99,102,241,0.4);">
                {{ strtoupper(substr($user->name,0,1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-white text-xs font-semibold truncate leading-none">{{ $user->name }}</p>
                <p class="text-indigo-400 text-[11px] mt-0.5">{{ $user->role->label() }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Sign out" class="text-indigo-500 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>
{{-- ── MAIN ─────────────────────────────────────────────────────────────── --}}
<div class="flex flex-col flex-1 min-w-0 overflow-hidden">
    {{-- Top bar --}}
    <header class="topbar shrink-0 px-6 py-3.5 sticky top-0 z-30">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-base font-bold text-gray-900 leading-tight">@yield('page_title','Dashboard')</h1>
                <p class="text-xs text-gray-400 mt-0.5">@yield('page_subtitle', now()->format('l, F j, Y'))</p>
            </div>
            <div class="flex items-center gap-2">
                {{-- Mobile nav --}}
                <nav class="flex flex-wrap items-center gap-1 lg:hidden">
                    @foreach($navItems as $item)
                        <a href="{{ route($item['route']) }}" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold {{ request()->routeIs($item['active']) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">{{ $item['label'] }}</a>
                    @endforeach
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200">Sign out</button></form>
                </nav>
                {{-- Notification bell --}}
                <div class="hidden lg:block relative" x-data="{open:false}" @click.outside="open=false">
                    <button @click="open=!open" class="relative flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 bg-white text-gray-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if($unreadCount>0)<span class="absolute -top-1 -right-1 flex items-center justify-center rounded-full text-[10px] font-bold text-white leading-none" style="background:linear-gradient(135deg,#ef4444,#dc2626);min-width:16px;min-height:16px;padding:0 3px;">{{ $unreadCount>9?'9+':$unreadCount }}</span>@endif
                    </button>
                    <div x-show="open" x-cloak class="absolute right-0 top-11 z-50 w-80 rounded-2xl border border-gray-100 bg-white shadow-2xl overflow-hidden" style="box-shadow:0 20px 60px rgba(0,0,0,0.12);">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-50">
                            <p class="text-sm font-bold text-gray-900">Notifications</p>
                            @if($unreadCount>0)<form method="POST" action="{{ route('notifications.mark-all-read') }}">@csrf<button type="submit" class="text-xs font-semibold text-indigo-600 hover:underline">Mark all read</button></form>@endif
                        </div>
                        <ul class="max-h-72 overflow-y-auto">
                            @forelse($recentNotifs as $notif)
                                <li><a href="{{ route('notifications.read',$notif) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0 {{ $notif->isUnread()?'bg-indigo-50/40':'' }}">
                                    <div class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $notif->isUnread()?'bg-indigo-500':'bg-transparent' }}"></div>
                                    <div class="min-w-0"><p class="text-xs font-semibold text-gray-800 truncate">{{ $notif->title }}</p>@if($notif->body)<p class="text-xs text-gray-500 truncate mt-0.5">{{ $notif->body }}</p>@endif<p class="text-[11px] text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p></div>
                                </a></li>
                            @empty
                                <li class="px-4 py-8 text-center text-xs text-gray-400">No notifications yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                {{-- User chip --}}
                <div class="hidden lg:flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 shadow-sm">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">{{ strtoupper(substr($user->name,0,1)) }}</div>
                    <div><p class="text-xs font-semibold text-gray-800 leading-none">{{ $user->name }}</p><p class="text-[11px] text-gray-400 mt-0.5">{{ $user->role->label() }}</p></div>
                </div>
            </div>
        </div>
    </header>
    {{-- Content --}}
    <main class="flex-1 overflow-auto p-6">
        @if(session('status'))
            <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-green-50 px-4 py-3.5 text-sm font-medium text-emerald-800 shadow-sm animate-fade-up">
                <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center shrink-0"><svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>
                {{ session('status') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-5 flex items-start gap-3 rounded-2xl border border-red-200 bg-gradient-to-r from-red-50 to-rose-50 px-4 py-3.5 text-sm text-red-800 shadow-sm animate-fade-up">
                <div class="w-6 h-6 rounded-full bg-red-500 flex items-center justify-center shrink-0 mt-0.5"><svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></div>
                <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
</div>
</div>
@else
{{-- ── GUEST LAYOUT ─────────────────────────────────────────────────────── --}}
<div class="min-h-full flex flex-col" style="background:linear-gradient(145deg,#f0f2ff 0%,#ece8ff 50%,#f0f2ff 100%);">
    <div class="fixed inset-0 pointer-events-none overflow-hidden" style="z-index:0;">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full opacity-25" style="background:radial-gradient(circle,#818cf8,transparent 70%);filter:blur(60px);"></div>
        <div class="absolute top-1/2 -left-32 w-80 h-80 rounded-full opacity-15" style="background:radial-gradient(circle,#a78bfa,transparent 70%);filter:blur(60px);"></div>
        <div class="absolute bottom-0 right-1/3 w-72 h-72 rounded-full opacity-15" style="background:radial-gradient(circle,#6366f1,transparent 70%);filter:blur(60px);"></div>
    </div>
    <header class="relative z-10 sticky top-0" style="background:rgba(255,255,255,0.88);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border-bottom:1px solid rgba(99,102,241,0.1);box-shadow:0 1px 0 rgba(0,0,0,0.04),0 4px 20px rgba(99,102,241,0.05);">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-6 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl transition-transform group-hover:scale-105" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 4px 14px rgba(79,70,229,0.4);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="text-lg font-bold text-gray-900 tracking-tight">HelpDesk</span>
            </a>
            <nav class="flex items-center gap-1.5">
                <a href="{{ route('knowledge-base.index') }}" class="hidden sm:inline-flex rounded-xl px-4 py-2 text-sm font-medium text-gray-600 hover:bg-white/80 hover:text-gray-900 transition-all">Knowledge Base</a>
                <a href="{{ route('login') }}" class="rounded-xl px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white/80 transition-all">Log in</a>
                <a href="{{ route('register') }}" class="rounded-xl px-4 py-2 text-sm font-bold text-white transition-all hover:-translate-y-0.5" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 4px 14px rgba(79,70,229,0.35);">Register</a>
            </nav>
        </div>
    </header>
    <main class="relative z-10 mx-auto w-full max-w-6xl flex-1 px-6 py-12">@yield('content')</main>
    <footer class="relative z-10 border-t py-6 text-center text-xs text-gray-400 font-medium" style="background:rgba(255,255,255,0.6);border-color:rgba(99,102,241,0.1);">© {{ date('Y') }} Student IT Help Desk · All rights reserved</footer>
</div>
@endauth
</body>
</html>
