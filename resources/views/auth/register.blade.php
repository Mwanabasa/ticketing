@extends('layouts.app')

@section('title', 'Create account')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-4xl animate-fade-in-up">
        <div class="bg-white rounded-3xl overflow-hidden flex flex-col lg:flex-row"
             style="box-shadow: 0 24px 80px rgba(14,165,233,0.15), 0 4px 24px rgba(0,0,0,0.08);">

            {{-- Left panel --}}
            <div class="lg:w-5/12 p-10 flex flex-col justify-between text-white relative overflow-hidden"
                 style="background: linear-gradient(145deg, #0c1a2e 0%, #0f2744 35%, #1e3a5f 65%, #0ea5e9 100%);">

                {{-- Orbs --}}
                <div class="absolute top-0 right-0 w-64 h-64 rounded-full opacity-20"
                     style="background: radial-gradient(circle, #38bdf8, transparent 65%); filter: blur(40px); transform: translate(30%, -30%);"></div>
                <div class="absolute bottom-0 left-0 w-56 h-56 rounded-full opacity-15"
                     style="background: radial-gradient(circle, #818cf8, transparent 65%); filter: blur(40px); transform: translate(-30%, 30%);"></div>
                <div class="absolute inset-0 opacity-[0.06]"
                     style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 28px 28px;"></div>

                <div class="relative">
                    <div class="flex items-center gap-3 mb-12">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center"
                             style="background: linear-gradient(135deg, #0ea5e9, #6366f1); box-shadow: 0 4px 16px rgba(14,165,233,0.5);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight">HelpDesk</span>
                    </div>
                    <h1 class="text-3xl font-extrabold leading-tight mb-4 tracking-tight">
                        Get IT support<br>in minutes
                    </h1>
                    <p class="text-sky-200 text-sm leading-relaxed">
                        Create a free student account and start submitting support tickets right away.
                    </p>
                </div>

                <div class="relative mt-10 space-y-3">
                    @foreach ([
                        ['step' => '1', 'text' => 'Create your free account'],
                        ['step' => '2', 'text' => 'Submit your support ticket'],
                        ['step' => '3', 'text' => 'Get help from IT staff'],
                    ] as $step)
                        <div class="flex items-center gap-3 rounded-2xl px-4 py-3"
                             style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                                 style="background: rgba(255,255,255,0.15);">
                                {{ $step['step'] }}
                            </div>
                            <span class="text-sm font-medium text-sky-100">{{ $step['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right panel --}}
            <div class="lg:w-7/12 p-10 flex flex-col justify-center bg-white">
                <div class="mb-8">
                    <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Create your account</h2>
                    <p class="text-gray-400 text-sm mt-1.5">New accounts are registered as students.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                               placeholder="John Doe"
                               class="w-full rounded-2xl border-2 px-4 py-3 text-sm transition-all duration-150 outline-none font-medium
                                      {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 focus:border-indigo-500 focus:bg-white focus:shadow-[0_0_0_4px_rgba(99,102,241,0.1)]' }}">
                        @error('name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                               placeholder="you@example.com"
                               class="w-full rounded-2xl border-2 px-4 py-3 text-sm transition-all duration-150 outline-none font-medium
                                      {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 focus:border-indigo-500 focus:bg-white focus:shadow-[0_0_0_4px_rgba(99,102,241,0.1)]' }}">
                        @error('email') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <input id="password" name="password" type="password" required autocomplete="new-password"
                                   placeholder="••••••••"
                                   class="w-full rounded-2xl border-2 px-4 py-3 text-sm transition-all duration-150 outline-none font-medium
                                          {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 focus:border-indigo-500 focus:bg-white focus:shadow-[0_0_0_4px_rgba(99,102,241,0.1)]' }}">
                            @error('password') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                   placeholder="••••••••"
                                   class="w-full rounded-2xl border-2 border-gray-200 bg-gray-50 px-4 py-3 text-sm transition-all duration-150 outline-none font-medium focus:border-indigo-500 focus:bg-white focus:shadow-[0_0_0_4px_rgba(99,102,241,0.1)]">
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full rounded-2xl py-3.5 text-sm font-bold text-white transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 mt-2"
                            style="background: linear-gradient(135deg, #0ea5e9, #6366f1); box-shadow: 0 8px 24px rgba(14,165,233,0.35);">
                        Create account
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-gray-500">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-700 transition-colors">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
