@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
    <div class="mx-auto max-w-2xl text-center">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Student IT Help Desk</h1>
        <p class="mt-4 text-lg text-slate-600">
            Submit WiFi, portal, password, or lab issues. Track your tickets and message support staff in one place.
        </p>
        <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
            @guest
                <a href="{{ route('register') }}" class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-800">
                    Create student account
                </a>
                <a href="{{ route('login') }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                    Log in
                </a>
            @else
                @if (auth()->user()->isStaff())
                    <a href="{{ route('admin.dashboard') }}" class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-800">
                        Open staff dashboard
                    </a>
                @else
                    <a href="{{ route('student.tickets.create') }}" class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-800">
                        Open a ticket
                    </a>
                    <a href="{{ route('student.dashboard') }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        My dashboard
                    </a>
                @endif
            @endguest
        </div>
        <p class="mt-12 rounded-lg border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-600 shadow-sm">
            <span class="font-medium text-slate-800">Demo logins (after <code class="rounded bg-slate-100 px-1">php artisan migrate --seed</code>):</span><br>
            Staff — <code class="text-slate-900">support@helpdesk.test</code> / <code class="text-slate-900">password</code><br>
            Student — <code class="text-slate-900">student@helpdesk.test</code> / <code class="text-slate-900">password</code>
        </p>
    </div>
@endsection
