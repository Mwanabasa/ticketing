<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full bg-slate-50 text-slate-900 antialiased">
    <div class="flex min-h-full flex-col">
        <header class="border-b border-slate-200 bg-white shadow-sm">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-4">
                <a href="{{ route('home') }}" class="text-lg font-semibold tracking-tight text-slate-900">
                    {{ config('app.name') }}
                </a>
                <nav class="flex flex-wrap items-center gap-4 text-sm font-medium text-slate-600">
                    @auth
                        @if (auth()->user()->isStaff())
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900">Staff dashboard</a>
                            <a href="{{ route('admin.tickets.index') }}" class="hover:text-slate-900">All tickets</a>
                            <a href="{{ route('admin.reports.index') }}" class="hover:text-slate-900">Reports</a>
                        @else
                            <a href="{{ route('student.dashboard') }}" class="hover:text-slate-900">My dashboard</a>
                            <a href="{{ route('student.tickets.index') }}" class="hover:text-slate-900">My tickets</a>
                            <a href="{{ route('student.tickets.create') }}" class="hover:text-slate-900">New ticket</a>
                        @endif
                        <span class="text-slate-400">|</span>
                        <span class="text-slate-500">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-slate-500 hover:text-slate-900">Log out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-slate-900">Log in</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-slate-900 px-3 py-1.5 text-white hover:bg-slate-800">Register</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
            Student IT Help Desk — coursework demo
        </footer>
    </div>
</body>
</html>
