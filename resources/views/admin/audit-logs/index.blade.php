@extends('layouts.app')

@section('title', 'Audit Logs')
@section('page_title', 'Audit Logs')

@section('content')
    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="mb-5 flex flex-wrap gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search model type, ID, or IP…"
               class="rounded-xl border border-slate-300 px-4 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 w-64">
        <select name="action" class="rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            <option value="">All actions</option>
            @foreach ($actions as $action)
                <option value="{{ $action }}" @selected(request('action') === $action)>{{ ucfirst($action) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-xl bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">Filter</button>
        @if (request()->hasAny(['q', 'action', 'user_id']))
            <a href="{{ route('admin.audit-logs.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Clear</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">When</th>
                    <th class="px-5 py-3 text-left">User</th>
                    <th class="px-5 py-3 text-left">Action</th>
                    <th class="px-5 py-3 text-left">Model</th>
                    <th class="px-5 py-3 text-left">Changes</th>
                    <th class="px-5 py-3 text-left">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($logs as $log)
                    <tr class="hover:bg-slate-50 transition align-top">
                        <td class="px-5 py-3.5 text-xs text-slate-400 whitespace-nowrap">
                            {{ $log->created_at->format('M j, Y') }}<br>
                            <span class="text-slate-300">{{ $log->created_at->format('g:i A') }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if ($log->user)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                                        {{ $log->user->isStaff() ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-600' }}">
                                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-slate-800 text-xs">{{ $log->user->name }}</span>
                                </div>
                            @else
                                <span class="text-slate-400 text-xs">System</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $actionColor = match($log->action) {
                                    'created' => 'bg-emerald-100 text-emerald-700',
                                    'updated' => 'bg-blue-100 text-blue-700',
                                    'deleted' => 'bg-red-100 text-red-700',
                                    default   => 'bg-slate-100 text-slate-600',
                                };
                            @endphp
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $actionColor }}">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-slate-600">
                            <span class="font-medium">{{ class_basename($log->model_type) }}</span>
                            <span class="text-slate-400"> #{{ $log->model_id }}</span>
                        </td>
                        <td class="px-5 py-3.5 max-w-xs">
                            @if ($log->old_values || $log->new_values)
                                <details class="text-xs">
                                    <summary class="cursor-pointer text-indigo-600 hover:underline select-none">View diff</summary>
                                    <div class="mt-2 space-y-1">
                                        @if ($log->old_values)
                                            <div>
                                                <p class="font-semibold text-slate-500 mb-0.5">Before</p>
                                                <pre class="rounded-lg bg-red-50 border border-red-100 p-2 text-xs text-red-800 overflow-x-auto whitespace-pre-wrap break-all">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        @endif
                                        @if ($log->new_values)
                                            <div>
                                                <p class="font-semibold text-slate-500 mb-0.5">After</p>
                                                <pre class="rounded-lg bg-emerald-50 border border-emerald-100 p-2 text-xs text-emerald-800 overflow-x-auto whitespace-pre-wrap break-all">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        @endif
                                    </div>
                                </details>
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-xs text-slate-400 whitespace-nowrap">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-14 text-center">
                            <p class="font-semibold text-slate-700">No audit log entries</p>
                            <p class="mt-1 text-sm text-slate-400">Activity will appear here as tickets are created and updated.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-slate-200 p-4">{{ $logs->links() }}</div>
    </div>
@endsection
