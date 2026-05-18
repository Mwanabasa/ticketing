@extends('layouts.app')
@section('title', 'Edit User — '.$user->name)
@section('page_title', 'Edit User')
@section('page_subtitle', $user->name)

@section('content')
<div class="max-w-lg">
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-indigo-600 transition mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Users
    </a>

    {{-- Profile card --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, #f8faff, #f3f0ff);">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-xl font-extrabold text-white shadow-lg"
                     style="background: linear-gradient(135deg, {{ $user->isStaff() ? '#4f46e5, #7c3aed' : '#0ea5e9, #6366f1' }});">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="font-bold text-gray-900 text-lg">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-400">{{ $user->email }}</p>
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold mt-1
                        {{ $user->isStaff() ? 'bg-indigo-100 text-indigo-700' : 'bg-sky-100 text-sky-700' }}">
                        {{ $user->role->label() }}
                    </span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 space-y-4">
            @csrf @method('PATCH')

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none transition {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email address</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none transition {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                <select id="role" name="role" required
                        class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none transition {{ $errors->has('role') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}">
                    @foreach (\App\Enums\UserRole::cases() as $role)
                        <option value="{{ $role->value }}" @selected(old('role', $user->role->value) === $role->value)>{{ $role->label() }}</option>
                    @endforeach
                </select>
                @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="rounded-xl px-6 py-2.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5"
                        style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    Save Changes
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="rounded-xl border-2 border-gray-200 px-6 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    {{-- Reset password --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-5">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <div>
                <p class="font-bold text-gray-900">Reset Password</p>
                <p class="text-xs text-gray-400">Set a new password for this account.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2">New password</label>
                    <input type="password" id="new_password" name="password" required placeholder="••••••••"
                           class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none transition {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm</label>
                    <input type="password" id="new_password_confirmation" name="password_confirmation" required placeholder="••••••••"
                           class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 transition">
                </div>
            </div>
            <button type="submit" class="rounded-xl border-2 border-amber-200 bg-amber-50 px-5 py-2.5 text-sm font-bold text-amber-700 hover:bg-amber-100 transition">
                Reset Password
            </button>
        </form>
    </div>

    {{-- Danger zone --}}
    @if ($user->id !== auth()->id())
        <div class="bg-white rounded-2xl border-2 border-red-100 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <p class="font-bold text-red-800">Danger Zone</p>
                    <p class="text-xs text-red-500">Deleting this account is permanent and cannot be undone.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                  onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700 transition">
                    Delete Account
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
