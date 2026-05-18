@extends('layouts.app')

@section('title', 'Verify your email')

@section('content')
    <div class="flex min-h-[70vh] items-center justify-center px-4">
        <div class="w-full max-w-md text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-100 mb-6">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900">Check your email</h1>
            <p class="mt-3 text-sm text-slate-500 leading-relaxed">
                We sent a verification link to <strong>{{ auth()->user()->email }}</strong>.<br>
                Click the link in that email to activate your account.
            </p>

            @if (session('status') === 'verification-link-sent')
                <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    A new verification link has been sent to your email.
                </div>
            @endif

            <div class="mt-6 space-y-3">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                            class="w-full rounded-xl bg-indigo-600 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                        Resend verification email
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full rounded-xl border border-slate-300 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
