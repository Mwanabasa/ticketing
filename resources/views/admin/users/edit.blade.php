@extends('layouts.app')

@section('title', 'Edit User — '.$user->name)
@section('page_title', 'Edit User')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-indigo-600 hover:underline">← All users</a>
    </div>

    <div class="mx-auto max-w-lg space-y-5">
        {{-- Profile details --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900 mb-5">Profile details</h2>
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                @csrf @method('PATCH')

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Full name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus
                           class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('name') border-red-400 @enderror">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('email') border-red-400 @enderror">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                    <select id="role" name="role" required
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('role') border-red-400 @enderror">
                        @foreach (\App\Enums\UserRole::cases() as $role)
                            <option value="{{ $role->value }}" @selected(old('role', $user->role->value) === $role->value)>{{ $role->label() }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                        Save changes
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        {{-- Reset password --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900 mb-1">Reset password</h2>
            <p class="text-sm text-slate-400 mb-4">Set a new password for this account.</p>
            <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="space-y-4">
                @csrf @method('PATCH')

                <div>
                    <label for="new_password" class="block text-sm font-medium text-slate-700 mb-1">New password</label>
                    <input type="password" id="new_password" name="password" required
                           class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('password') border-red-400 @enderror">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm new password</label>
                    <input type="password" id="new_password_confirmation" name="password_confirmation" required
                           class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                </div>

                <button type="submit" class="rounded-xl border border-slate-300 bg-slate-50 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
                    Reset password
                </button>
            </form>
        </div>

        {{-- Danger zone --}}
        @if ($user->id !== auth()->id())
            <div class="rounded-2xl border border-red-200 bg-red-50 p-6 shadow-sm">
                <h2 class="text-base font-semibold text-red-800 mb-1">Danger zone</h2>
                <p class="text-sm text-red-600 mb-4">Deleting this account is permanent and cannot be undone.</p>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                      onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition">
                        Delete account
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
