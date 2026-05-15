<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full bg-sky-200 text-slate-900 antialiased">
    @auth
        @php
            $user = auth()->user();
            $navItems = $user->isStaff()
                ? [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
                    ['label' => 'All tickets', 'route' => 'admin.tickets.index', 'active' => 'admin.tickets.*'],
                    ['label' => 'Reports', 'route' => 'admin.reports.index', 'active' => 'admin.reports.*'],
                ]
                : [
                    ['label' => 'Dashboard', 'route' => 'student.dashboard', 'active' => 'student.dashboard'],
                    ['label' => 'My tickets', 'route' => 'student.tickets.index', 'active' => 'student.tickets.index'],
                    ['label' => 'New ticket', 'route' => 'student.tickets.create', 'active' => 'student.tickets.create'],
                ];
        @endphp

        <div class="min-h-full p-3 sm:p-6">
            <div class="mx-auto flex min-h-[calc(100vh-3rem)] max-w-[92rem] overflow-hidden rounded-2xl bg-slate-50 shadow-2xl ring-1 ring-black/5">
                <aside class="hidden w-64 shrink-0 bg-indigo-950 text-white lg:flex lg:flex-col">
                    <div class="px-6 py-6">
                        <a href="{{ route('home') }}" class="block border-b border-white/25 pb-4 text-xl font-black tracking-tight">
                            HELPDESK
                        </a>
                    </div>

                    <nav class="flex-1 space-y-1 px-3">
                        @foreach ($navItems as $item)
                            <a href="{{ route($item['route']) }}" class="flex items-center justify-between rounded-lg px-4 py-3 text-sm font-semibold transition {{ request()->routeIs($item['active']) ? 'bg-rose-600 text-white shadow-lg shadow-rose-950/20' : 'text-indigo-100 hover:bg-white/10 hover:text-white' }}">
                                <span>{{ $item['label'] }}</span>
                                @if (request()->routeIs($item['active']))
                                    <span class="size-2 rounded-full bg-white"></span>
                                @endif
                            </a>
                        @endforeach
                    </nav>

                    <div class="border-t border-white/10 p-4">
                        <div class="rounded-xl bg-white/10 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-200">{{ $user->role->label() }}</p>
                            <p class="mt-1 truncate text-sm font-semibold text-white">{{ $user->name }}</p>
                        </div>
                    </div>
                </aside>

                <div class="flex min-w-0 flex-1 flex-col">
                    <header class="bg-gradient-to-r from-indigo-600 to-sky-500 px-4 py-4 text-white sm:px-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-white/70">{{ $user->isStaff() ? 'Staff workspace' : 'Student workspace' }}</p>
                                <h1 class="mt-1 text-2xl font-bold tracking-tight">@yield('page_title', 'Dashboard')</h1>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 text-sm">
                                <nav class="flex flex-wrap gap-1 lg:hidden">
                                    @foreach ($navItems as $item)
                                        <a href="{{ route($item['route']) }}" class="rounded-lg px-3 py-2 font-semibold {{ request()->routeIs($item['active']) ? 'bg-white text-indigo-700' : 'bg-white/10 text-white hover:bg-white/20' }}">
                                            {{ $item['label'] }}
                                        </a>
                                    @endforeach
                                </nav>
                                <span class="hidden rounded-full bg-white/15 px-3 py-2 font-semibold sm:inline">{{ $user->name }}</span>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-white/15 px-3 py-2 font-semibold hover:bg-white/25">Log out</button>
                                </form>
                            </div>
                        </div>
                    </header>

                    <main class="flex-1 overflow-auto bg-slate-50 p-4 sm:p-6">
                        @if (session('status'))
                            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 shadow-sm">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900 shadow-sm">
                                <ul class="list-inside list-disc space-y-1">
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
        </div>
    @else
        <div class="flex min-h-full flex-col bg-slate-100">
            <header class="border-b border-slate-200 bg-white shadow-sm">
                <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-4">
                    <a href="{{ route('home') }}" class="text-lg font-bold tracking-tight text-slate-950">
                        {{ config('app.name') }}
                    </a>
                    <nav class="flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-600">
                        <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-950">Log in</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-slate-950 px-3 py-2 text-white shadow-sm hover:bg-slate-800">Register</a>
                    </nav>
                </div>
            </header>

            <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
                @yield('content')
            </main>

            <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
                Student IT Help Desk
            </footer>
        </div>
    @endauth
</body>
</html>
