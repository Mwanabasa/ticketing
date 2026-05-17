@extends('layouts.app')

@section('title', 'User Management')
@section('page_title', 'User Management')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or email…"
                   class="rounded-xl border border-slate-300 px-4 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 w-56">
            <select name="role" class="rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                <option value="">All roles</option>
                @foreach (\App\Enums\UserRole::cases() as $role)
                    <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">Filter</button>
            @if (request()->hasAny(['q', 'role']))
                <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Clear</a>
            @endif
        </form>
        <a href="{{ route('admin.users.create') }}" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            + New user
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Role</th>
                    <th class="px-5 py-3 text-left">Joined</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $user)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                                    {{ $user->isStaff() ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-700' }}">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-slate-900">{{ $user->name }}</span>
                                @if ($user->id === auth()->id())
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">You</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500">{{ $user->email }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold
                                {{ $user->isStaff() ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $user->role->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $user->created_at->format('M j, Y') }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-sm font-medium text-indigo-600 hover:underline">Edit</a>
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-500 hover:underline">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-14 text-center">
                            <p class="font-semibold text-slate-700">No users found</p>
                            <p class="mt-1 text-sm text-slate-400">Try adjusting your filters or create a new user.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-slate-200 p-4">{{ $users->links() }}</div>
    </div>
@endsection
