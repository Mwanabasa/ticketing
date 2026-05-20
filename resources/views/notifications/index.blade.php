@extends('layouts.app')
@section('title', 'Notifications')
@section('page_title', 'Notifications')
@section('page_subtitle', 'Your recent activity and updates')

@section('content')
<div class="max-w-2xl">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100"
             style="background: linear-gradient(135deg, #f8faff, #f3f0ff);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                     style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">All Notifications</h3>
                    <p class="text-xs text-slate-400">{{ $notifications->total() }} total</p>
                </div>
            </div>
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Mark all read
                </button>
            </form>
        </div>

        @forelse ($notifications as $notif)
            @php
                $isUnread = !$notif->read_at;
                $iconMap = [
                    'reply'          => ['bg' => '#dbeafe', 'color' => '#2563eb', 'path' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                    'status_changed' => ['bg' => '#d1fae5', 'color' => '#059669', 'path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'assigned'       => ['bg' => '#ede9fe', 'color' => '#7c3aed', 'path' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ];
                $icon = $iconMap[$notif->type] ?? ['bg' => '#f1f5f9', 'color' => '#64748b', 'path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'];
            @endphp
            <div class="relative flex items-start gap-4 px-6 py-4 border-b border-slate-50 hover:bg-slate-50/60 transition-colors
                        {{ $isUnread ? 'bg-indigo-50/30' : '' }}">

                {{-- Unread left accent --}}
                @if ($isUnread)
                    <div class="absolute left-0 top-3 bottom-3 w-0.5 rounded-r-full bg-indigo-500"></div>
                @endif

                {{-- Type icon --}}
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
                     style="background: {{ $icon['bg'] }};">
                    <svg class="w-4 h-4" style="color: {{ $icon['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon['path'] }}"/>
                    </svg>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-slate-900 leading-snug">{{ $notif->title }}</p>
                        @if ($isUnread)
                            <span class="w-2 h-2 rounded-full bg-indigo-500 shrink-0 mt-1.5"></span>
                        @endif
                    </div>
                    @if ($notif->body)
                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-2 leading-relaxed">{{ $notif->body }}</p>
                    @endif
                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium">{{ $notif->created_at->diffForHumans() }}</p>
                </div>

                @if ($notif->url)
                    <a href="{{ $notif->url }}"
                       class="shrink-0 rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-600 transition-all">
                        View →
                    </a>
                @endif
            </div>
        @empty
            <div class="py-24 text-center px-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-5"
                     style="background: linear-gradient(135deg, #eef2ff, #e0e7ff);">
                    <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="font-bold text-slate-700 text-lg mb-1">All caught up!</p>
                <p class="text-sm text-slate-400 max-w-xs mx-auto">You'll be notified here when tickets are updated, assigned, or replied to.</p>
            </div>
        @endforelse

        @if ($notifications->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
