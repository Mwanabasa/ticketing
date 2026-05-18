@extends('layouts.app')

@section('title', 'Welcome')

@section('content')

    {{-- ── HERO ──────────────────────────────────────────────────────────────── --}}
    <div class="relative text-center py-24 px-4 overflow-hidden">

        {{-- Animated orbs --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-8 left-1/4 w-72 h-72 rounded-full opacity-25 animate-float"
                 style="background: radial-gradient(circle, #818cf8, transparent 65%); filter: blur(40px); animation-delay: 0s;"></div>
            <div class="absolute top-16 right-1/4 w-64 h-64 rounded-full opacity-20 animate-float"
                 style="background: radial-gradient(circle, #a78bfa, transparent 65%); filter: blur(40px); animation-delay: 1.5s;"></div>
            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-96 h-48 rounded-full opacity-15"
                 style="background: radial-gradient(circle, #6366f1, transparent 65%); filter: blur(50px);"></div>
        </div>

        <div class="relative">
            {{-- Pill badge --}}
            <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-semibold text-indigo-700 mb-8 animate-fade-in-up"
                 style="background: linear-gradient(135deg, rgba(238,242,255,0.9), rgba(237,233,254,0.9)); border: 1px solid rgba(99,102,241,0.2); backdrop-filter: blur(8px); box-shadow: 0 2px 12px rgba(99,102,241,0.12);">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                Student IT Support Portal
            </div>

            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-gray-900 leading-[1.08] tracking-tight mb-6 animate-fade-in-up stagger-1">
                Get IT help,<br>
                <span style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    fast and easy.
                </span>
            </h1>

            <p class="text-lg sm:text-xl text-gray-500 max-w-xl mx-auto leading-relaxed mb-10 animate-fade-in-up stagger-2">
                Submit WiFi, portal, password, or lab issues. Track your tickets and message support staff — all in one place.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-4 animate-fade-in-up stagger-3">
                @guest
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 rounded-2xl px-8 py-4 text-base font-bold text-white transition-all duration-200 hover:-translate-y-1"
                       style="background: linear-gradient(135deg, #4f46e5, #7c3aed); box-shadow: 0 8px 28px rgba(79,70,229,0.45);">
                        Get started free
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 rounded-2xl border-2 border-gray-200 bg-white/80 backdrop-blur px-8 py-4 text-base font-bold text-gray-700 hover:border-indigo-300 hover:bg-white hover:shadow-lg transition-all duration-200">
                        Sign in
                    </a>
                @else
                    @if (auth()->user()->isStaff())
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center gap-2 rounded-2xl px-8 py-4 text-base font-bold text-white transition-all duration-200 hover:-translate-y-1"
                           style="background: linear-gradient(135deg, #4f46e5, #7c3aed); box-shadow: 0 8px 28px rgba(79,70,229,0.45);">
                            Open staff dashboard
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    @else
                        <a href="{{ route('student.tickets.create') }}"
                           class="inline-flex items-center gap-2 rounded-2xl px-8 py-4 text-base font-bold text-white transition-all duration-200 hover:-translate-y-1"
                           style="background: linear-gradient(135deg, #4f46e5, #7c3aed); box-shadow: 0 8px 28px rgba(79,70,229,0.45);">
                            Open a ticket
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <a href="{{ route('student.dashboard') }}"
                           class="inline-flex items-center gap-2 rounded-2xl border-2 border-gray-200 bg-white/80 backdrop-blur px-8 py-4 text-base font-bold text-gray-700 hover:border-indigo-300 hover:bg-white hover:shadow-lg transition-all duration-200">
                            My dashboard
                        </a>
                    @endif
                @endguest
            </div>

            {{-- Trust indicators --}}
            <div class="flex flex-wrap items-center justify-center gap-6 mt-12 animate-fade-in-up stagger-4">
                @foreach ([['🔒', 'Secure & private'], ['⚡', 'Fast response'], ['📱', 'Works on any device']] as $t)
                    <div class="flex items-center gap-2 text-sm text-gray-500 font-medium">
                        <span>{{ $t[0] }}</span>
                        <span>{{ $t[1] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── FEATURE CARDS ─────────────────────────────────────────────────────── --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 max-w-5xl mx-auto mb-24">
        @foreach ([
            ['emoji' => '📶', 'title' => 'WiFi Issues',    'desc' => 'Report connectivity problems on campus.',    'from' => '#4f46e5', 'to' => '#6366f1', 'bg' => '#eef2ff', 'delay' => '0s'],
            ['emoji' => '🖥️', 'title' => 'Portal Issues',  'desc' => 'Get help with student portal access.',       'from' => '#7c3aed', 'to' => '#8b5cf6', 'bg' => '#ede9fe', 'delay' => '0.05s'],
            ['emoji' => '🔑', 'title' => 'Password Reset', 'desc' => 'Recover access to your accounts quickly.',   'from' => '#0ea5e9', 'to' => '#38bdf8', 'bg' => '#e0f2fe', 'delay' => '0.10s'],
            ['emoji' => '🖨️', 'title' => 'Computer Lab',   'desc' => 'Report hardware or software lab issues.',    'from' => '#10b981', 'to' => '#34d399', 'bg' => '#d1fae5', 'delay' => '0.15s'],
        ] as $f)
            <div class="group relative bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 overflow-hidden animate-fade-in-up"
                 style="animation-delay: {{ $f['delay'] }};">
                {{-- Hover gradient overlay --}}
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-3xl"
                     style="background: linear-gradient(135deg, {{ $f['bg'] }}80, transparent);"></div>
                <div class="relative">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mb-5 transition-transform duration-300 group-hover:scale-110"
                         style="background: linear-gradient(135deg, {{ $f['from'] }}18, {{ $f['to'] }}28);">
                        {{ $f['emoji'] }}
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2 text-base">{{ $f['title'] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── HOW IT WORKS ──────────────────────────────────────────────────────── --}}
    <div class="max-w-3xl mx-auto text-center mb-24">
        <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold text-indigo-600 uppercase tracking-widest mb-4"
             style="background: #eef2ff; border: 1px solid #c7d2fe;">How it works</div>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-3 tracking-tight">Three steps to resolution</h2>
        <p class="text-gray-400 mb-14 text-lg">Simple, fast, and transparent support.</p>

        <div class="grid gap-8 sm:grid-cols-3 relative">
            {{-- Connector line --}}
            <div class="hidden sm:block absolute top-7 left-[calc(16.67%+1rem)] right-[calc(16.67%+1rem)] h-px"
                 style="background: linear-gradient(90deg, #c7d2fe, #a5b4fc, #c7d2fe);"></div>

            @foreach ([
                ['step' => '1', 'title' => 'Submit a ticket', 'desc' => 'Describe your issue and choose a category.', 'from' => '#4f46e5', 'to' => '#6366f1'],
                ['step' => '2', 'title' => 'Staff responds',  'desc' => 'Support staff review and reply to your ticket.', 'from' => '#7c3aed', 'to' => '#8b5cf6'],
                ['step' => '3', 'title' => 'Issue resolved',  'desc' => 'Track progress until your issue is closed.', 'from' => '#10b981', 'to' => '#34d399'],
            ] as $s)
                <div class="flex flex-col items-center animate-fade-in-up">
                    <div class="relative w-14 h-14 rounded-2xl flex items-center justify-center text-white text-xl font-extrabold mb-5 z-10"
                         style="background: linear-gradient(135deg, {{ $s['from'] }}, {{ $s['to'] }}); box-shadow: 0 8px 24px {{ $s['from'] }}55;">
                        {{ $s['step'] }}
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2 text-base">{{ $s['title'] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $s['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── CTA BANNER ────────────────────────────────────────────────────────── --}}
    @guest
        <div class="max-w-3xl mx-auto mb-8 animate-fade-in-up">
            <div class="relative rounded-3xl p-10 text-center text-white overflow-hidden"
                 style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6d28d9 100%); box-shadow: 0 20px 60px rgba(79,70,229,0.4);">

                {{-- Dot grid --}}
                <div class="absolute inset-0 opacity-[0.07]"
                     style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 24px 24px;"></div>

                {{-- Glow orbs --}}
                <div class="absolute top-0 left-1/4 w-48 h-48 rounded-full opacity-20"
                     style="background: radial-gradient(circle, #a78bfa, transparent); filter: blur(30px);"></div>
                <div class="absolute bottom-0 right-1/4 w-48 h-48 rounded-full opacity-20"
                     style="background: radial-gradient(circle, #818cf8, transparent); filter: blur(30px);"></div>

                <div class="relative">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/20 px-3 py-1 text-xs font-bold uppercase tracking-widest text-indigo-200 mb-5">
                        ✨ Free to use
                    </div>
                    <h2 class="text-3xl font-extrabold mb-3 tracking-tight">Ready to get started?</h2>
                    <p class="text-indigo-200 mb-8 text-base max-w-md mx-auto">Create your free account and submit your first ticket in minutes.</p>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-bold text-indigo-700 shadow-xl hover:bg-indigo-50 hover:-translate-y-0.5 transition-all duration-200">
                        Create free account
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    @endguest

@endsection
