@extends('layouts.app')
@section('title', 'New User')
@section('page_title', 'New User')
@section('page_subtitle', 'Create a new staff or student account')

@section('content')
<div class="max-w-lg">
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-indigo-600 transition mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Users
    </a>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, #f8faff, #f3f0ff);">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900">New User Account</h2>
                    <p class="text-xs text-gray-400">Fill in the details below</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                       placeholder="John Doe"
                       class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none transition {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       placeholder="user@example.com"
                       class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none transition {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                <select id="role" name="role" required
                        class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none transition {{ $errors->has('role') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}">
                    <option value="">Select a role…</option>
                    @foreach (\App\Enums\UserRole::cases() as $role)
                        <option value="{{ $role->value }}" @selected(old('role') === $role->value)>{{ $role->label() }}</option>
                    @endforeach
                </select>
                @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••"
                           class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none transition {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••"
                           class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 transition">
                </div>
            </div>

            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="rounded-xl px-6 py-2.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5"
                        style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    Create User
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="rounded-xl border-2 border-gray-200 px-6 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
