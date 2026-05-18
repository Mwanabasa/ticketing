@extends('layouts.app')

@section('title', 'Sign in')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-4xl animate-fade-in-up">
        <div class="bg-white rounded-3xl overflow-hidden flex flex-col lg:flex-row"
             style="box-shadow: 0 24px 80px rgba(79,70,229,0.18), 0 4px 24px rgba(0,0,0,0.08);">

            {{-- Left panel --}}
            <div class="lg:w-5/12 p-10 flex flex-col justify-between text-white relative overflow-hidden"
                 style="background: linear-gradient(145deg, #0f0c29 0%, #1e1b4b 40%, #312e81 80%, #4338ca 100%);">

                {{-- Orbs --}}
                <div class="absolute top-0 right-0 w-64 h-64 rounded-full opacity-20"
                     style="background: radial-gradient(circle, #818cf8, transparent 65%); filter: blur(40px); transform: translate(30%, -30%);"></div>
                <div class="absolute bottom-0 left-0 w-56 h-56 rounded-full opacity-15"
                     style="background: radial-gradient(circle, #a78bfa, transparent 65%); filter: blur(40px); transform: translate(-30%, 30%);"></div>
                {{-- Dot grid --}}
                <div class="absolute inset-0 opacity-[0.06]"
                     style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 28px 28px;"></div>

                <div class="relative">
                    <div class="flex items-center gap-3 mb-12">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center"
                             style="background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 4px 16px rgba(99,102,241,0.5);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight">HelpDesk</span>
                    </div>
                    <h1 class="text-3xl font-extrabold leading-tight mb-4 tracking-tight">
                        Welcome back to<br>IT Support Portal
                    </h1>
                    <p class="text-indigo-300 text-sm leading-relaxed">
                        Submit tickets, track progress, and get help from our support team — all in one place.
                    </p>
                </div>

                <div class="relative mt-10 space-y-3">
                    @foreach ([
                        ['icon' => '🎫', 'text' => 'Submit and track support tickets'],
                        ['icon' => '💬', 'text' => 'Chat directly with IT staff'],
                        ['icon' => '📚', 'text' => 'Browse the knowledge base'],
                    ] as $feature)
                        <div class="flex items-center gap-3 rounded-2xl px-4 py-3"
                             style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);">
                            <span class="text-lg">{{ $feature['icon'] }}</span>
                            <span class="text-sm font-medium text-indigo-100">{{ $feature['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right panel --}}
            <div class="lg:w-7/12 p-10 flex flex-col justify-center bg-white">
                <div class="mb-8">
                    <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Sign in to your account</h2>
                    <p class="text-gray-400 text-sm mt-1.5">Students and staff use the same login.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                               placeholder="you@example.com"
                               class="w-full rounded-2xl border-2 px-4 py-3 text-sm transition-all duration-150 outline-none font-medium
                                      {{ $errors->has('email') ? 'border-red-400 bg-red-50 focus:border-red-500' : 'border-gray-200 bg-gray-50 focus:border-indigo-500 focus:bg-white focus:shadow-[0_0_0_4px_rgba(99,102,241,0.1)]' }}">
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">Forgot password?</a>
                        </div>
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full rounded-2xl border-2 border-gray-200 bg-gray-50 px-4 py-3 text-sm transition-all duration-150 outline-none font-medium focus:border-indigo-500 focus:bg-white focus:shadow-[0_0_0_4px_rgba(99,102,241,0.1)]">
                    </div>

                    <div class="flex items-center gap-2.5">
                        <input id="remember" name="remember" type="checkbox" value="1"
                               class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="remember" class="text-sm text-gray-600 font-medium">Keep me signed in</label>
                    </div>

                    <button type="submit"
                            class="w-full rounded-2xl py-3.5 text-sm font-bold text-white transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2"
                            style="background: linear-gradient(135deg, #4f46e5, #7c3aed); box-shadow: 0 8px 24px rgba(79,70,229,0.4);">
                        Sign in
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-gray-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-700 transition-colors">Create one free</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
