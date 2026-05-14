@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="mx-auto max-w-md">
        <h1 class="text-2xl font-bold text-slate-900">Student registration</h1>
        <p class="mt-1 text-sm text-slate-600">New accounts are created as students. Staff accounts are added by administrators.</p>

        <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
            <button type="submit" class="w-full rounded-lg bg-slate-900 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                Create account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            Already registered?
            <a href="{{ route('login') }}" class="font-medium text-slate-900 underline">Log in</a>
        </p>
    </div>
@endsection
