@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
    {{-- Hero --}}
    <div class="text-center py-16 px-4">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 mb-6">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
            Student IT Support Portal
        </span>
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
            Get IT help,<br class="hidden sm:block"> fast and easy.
        </h1>
        <p class="mt-5 text-lg text-slate-500 max-w-xl mx-auto leading-relaxed">
            Submit WiFi, portal, password, or lab issues. Track your tickets and message support staff — all in one place.
        </p>
        <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
            @guest
                <a href="{{ route('register') }}" class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition">
                    Create student account
                </a>
                <a href="{{ route('login') }}" class="rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                    Log in
                </a>
            @else
                @if (auth()->user()->isStaff())
                    <a href="{{ route('admin.dashboard') }}" class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition">
                        Open staff dashboard
                    </a>
                @else
                    <a href="{{ route('student.tickets.create') }}" class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition">
                        Open a ticket
                    </a>
                    <a href="{{ route('student.dashboard') }}" class="rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                        My dashboard
                    </a>
                @endif
            @endguest
        </div>
    </div>

    {{-- Feature cards --}}
    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4 max-w-5xl mx-auto">
        @foreach ([
            ['icon' => 'wifi',     'color' => 'indigo', 'title' => 'WiFi Issues',      'desc' => 'Report connectivity problems on campus.'],
            ['icon' => 'portal',   'color' => 'violet', 'title' => 'Portal Issues',     'desc' => 'Get help with student portal access.'],
            ['icon' => 'password', 'color' => 'sky',    'title' => 'Password Reset',    'desc' => 'Recover access to your accounts quickly.'],
            ['icon' => 'lab',      'color' => 'emerald','title' => 'Computer Lab',      'desc' => 'Report hardware or software lab issues.'],
        ] as $f)
            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4
                    {{ $f['color'] === 'indigo'  ? 'bg-indigo-100'  : '' }}
                    {{ $f['color'] === 'violet'  ? 'bg-violet-100'  : '' }}
                    {{ $f['color'] === 'sky'     ? 'bg-sky-100'     : '' }}
                    {{ $f['color'] === 'emerald' ? 'bg-emerald-100' : '' }}">
                    @if ($f['icon'] === 'wifi')
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                    @elseif ($f['icon'] === 'portal')
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    @elseif ($f['icon'] === 'password')
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    @else
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    @endif
                </div>
                <h3 class="font-semibold text-slate-900">{{ $f['title'] }}</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $f['desc'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- How it works --}}
    <div class="mt-20 max-w-3xl mx-auto text-center">
        <h2 class="text-2xl font-bold text-slate-900">How it works</h2>
        <div class="mt-10 grid gap-6 sm:grid-cols-3">
            @foreach ([
                ['step' => '1', 'title' => 'Submit a ticket', 'desc' => 'Describe your issue and choose a category.'],
                ['step' => '2', 'title' => 'Staff responds',  'desc' => 'Support staff review and reply to your ticket.'],
                ['step' => '3', 'title' => 'Issue resolved',  'desc' => 'Track progress until your issue is closed.'],
            ] as $s)
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white font-bold text-sm flex items-center justify-center shadow-lg shadow-indigo-200">{{ $s['step'] }}</div>
                    <h3 class="mt-4 font-semibold text-slate-900">{{ $s['title'] }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $s['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
