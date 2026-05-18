@extends('layouts.app')
@section('title', 'Audit Logs')
@section('page_title', 'Audit Logs')
@section('page_subtitle', 'Track all ticket activity and changes')

@section('content')

{{-- Filter bar --}}
<div class="card p-4 mb-5 animate-fade-up">
    <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="flex flex-wrap gap-2 items-center">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search model type, ID, or IP…"
                   class="input pl-9 py-2 text-sm">
        </div>
        <select name="action" class="input px-3 py-2 text-sm w-auto">
            <option value="">All actions</option>
            @foreach ($actions as $action)
                <option value="{{ $action }}" @selected(request('action') === $action)>{{ ucfirst($action) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary py-2 px-4 text-sm">Filter</button>
        @if (request()->hasAny(['q','action','user_id']))
            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary py-2 px-4 text-sm">Clear</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="card overflow-hidden animate-fade-up">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">When</th>
                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">User</th>
                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Action</th>
                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Model</th>
                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Changes</th>
                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">IP</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse ($logs as $log)
                <tr class="hover:bg-gray-50/60 transition-colors align-top">
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <p class="text-xs font-semibold text-gray-700">{{ $log->created_at->format('M j, Y') }}</p>
                        <p class="text-xs text-gray-400">{{ $log->created_at->format('g:i A') }}</p>
                    </td>
                    <td class="px-5 py-3.5">
                        @if ($log->user)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                                    {{ $log->user->isStaff() ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-200 text-gray-600' }}">
                                    {{ strtoupper(substr($log->user->name,0,1)) }}
                                </div>
                                <span class="text-xs font-semibold text-gray-800">{{ $log->user->name }}</span>
                            </div>
                        @else
                            <span class="text-xs text-gray-400 italic">System</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        @php
                            $actionStyle = match($log->action) {
                                'created' => 'background:#d1fae5;color:#065f46;',
                                'updated' => 'background:#dbeafe;color:#1e40af;',
                                'deleted' => 'background:#fee2e2;color:#991b1b;',
                                default   => 'background:#f3f4f6;color:#374151;',
                            };
                        @endphp
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold" style="{{ $actionStyle }}">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-xs">
                        <span class="font-semibold text-gray-800">{{ class_basename($log->model_type) }}</span>
                        <span class="text-gray-400"> #{{ $log->model_id }}</span>
                    </td>
                    <td class="px-5 py-3.5 max-w-xs">
                        @if ($log->old_values || $log->new_values)
                            <details class="text-xs">
                                <summary class="cursor-pointer text-indigo-600 hover:text-indigo-800 font-semibold select-none">View diff</summary>
                                <div class="mt-2 space-y-2">
                                    @if ($log->old_values)
                                        <div>
                                            <p class="text-xs font-bold text-gray-500 mb-1">Before</p>
                                            <pre class="rounded-xl bg-red-50 border border-red-100 p-2 text-xs text-red-800 overflow-x-auto whitespace-pre-wrap break-all">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    @endif
                                    @if ($log->new_values)
                                        <div>
                                            <p class="text-xs font-bold text-gray-500 mb-1">After</p>
                                            <pre class="rounded-xl bg-emerald-50 border border-emerald-100 p-2 text-xs text-emerald-800 overflow-x-auto whitespace-pre-wrap break-all">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    @endif
                                </div>
                            </details>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-xs text-gray-400 whitespace-nowrap font-mono">{{ $log->ip_address ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gray-100 mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <p class="font-semibold text-gray-600">No audit log entries</p>
                        <p class="mt-1 text-sm text-gray-400">Activity will appear here as tickets are created and updated.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="border-t border-gray-100 px-5 py-4">{{ $logs->links() }}</div>
</div>
@endsection
