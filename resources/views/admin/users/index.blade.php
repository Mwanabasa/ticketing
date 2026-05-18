@extends('layouts.app')
@section('title', 'Users')
@section('page_title', 'User Management')
@section('page_subtitle', 'Manage staff and student accounts')

@section('content')
@php
    $totalStaff    = $users->getCollection()->where('role.value', 'staff')->count();
    $totalStudents = $users->getCollection()->where('role.value', 'student')->count();
@endphp

{{-- Stat cards --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    @foreach ([
        ['label'=>'Total Users',   'val'=>$users->total(), 'icon'=>'users',   'color'=>'#4f46e5','bg'=>'#eef2ff'],
        ['label'=>'Staff Members', 'val'=>$totalStaff,     'icon'=>'shield',  'color'=>'#7c3aed','bg'=>'#ede9fe'],
        ['label'=>'Students',      'val'=>$totalStudents,  'icon'=>'academic','color'=>'#0ea5e9','bg'=>'#e0f2fe'],
    ] as $s)
        <div class="card p-5 animate-fade-up">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:{{ $s['bg'] }};">
                @if($s['icon']==='users')
                    <svg class="w-5 h-5" style="color:{{ $s['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                @elseif($s['icon']==='shield')
                    <svg class="w-5 h-5" style="color:{{ $s['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                @else
                    <svg class="w-5 h-5" style="color:{{ $s['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                @endif
            </div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $s['val'] }}</p>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-1">{{ $s['label'] }}</p>
        </div>
    @endforeach
</div>

{{-- Table card --}}
<div class="card animate-fade-up">
    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-gray-100">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-2">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or email…" class="input pl-9 py-2 text-sm w-56">
            </div>
            <select name="role" class="input px-3 py-2 text-sm w-auto">
                <option value="">All roles</option>
                @foreach (\App\Enums\UserRole::cases() as $role)
                    <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary py-2 px-4 text-sm">Filter</button>
            @if (request()->hasAny(['q','role']))
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary py-2 px-4 text-sm">Clear</a>
            @endif
        </form>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New User
        </a>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">User</th>
                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Email</th>
                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Role</th>
                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Joined</th>
                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse ($users as $user)
                <tr class="hover:bg-gray-50/80 transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                                 style="background:linear-gradient(135deg,{{ $user->isStaff() ? '#4f46e5,#7c3aed' : '#0ea5e9,#6366f1' }});">
                                {{ strtoupper(substr($user->name,0,1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                @if ($user->id === auth()->id())
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">You</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-500 text-sm">{{ $user->email }}</td>
                    <td class="px-5 py-4">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $user->isStaff() ? 'bg-indigo-100 text-indigo-700' : 'bg-sky-100 text-sky-700' }}">
                            {{ $user->role->label() }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-gray-400 text-xs">{{ $user->created_at->format('M j, Y') }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">Edit</a>
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-red-400 hover:text-red-600 transition-colors">Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-16 text-center">
                        <p class="font-semibold text-gray-600">No users found</p>
                        <p class="mt-1 text-sm text-gray-400">Try adjusting your filters.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="border-t border-gray-100 px-5 py-4">{{ $users->links() }}</div>
</div>
@endsection
