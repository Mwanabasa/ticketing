@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <div class="mx-auto max-w-md">
        <h1 class="text-2xl font-bold text-slate-900">Log in</h1>
        <p class="mt-1 text-sm text-slate-600">Students and support staff use the same page.</p>

        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
            <div class="flex items-center gap-2">
                <input id="remember" name="remember" type="checkbox" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                <label for="remember" class="text-sm text-slate-600">Remember me</label>
            </div>
            <button type="submit" class="w-full rounded-lg bg-slate-900 py-2.5 text-sm font-semibold text-white">
                Log in
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            No account?
            <a href="{{ route('register') }}" class="font-medium text-slate-900 underline">Register as a student</a>
        </p>
    </div>
@endsection
