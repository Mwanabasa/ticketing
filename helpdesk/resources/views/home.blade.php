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
                <a href="{{ route('register') }}" class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow">
                    Create student account
                </a>
                <a href="{{ route('login') }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800">
                    Log in
                </a>
            @else
                @if (auth()->user()->isStaff())
                    <a href="{{ route('admin.dashboard') }}" class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow">
                        Open staff dashboard
                    </a>
                @else
                    <a href="{{ route('student.tickets.create') }}" class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow">
                        Open a ticket
                    </a>
                    <a href="{{ route('student.dashboard') }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800">
                        My dashboard
                    </a>
                @endif
            @endguest
        </div>
    </div>
@endsection
