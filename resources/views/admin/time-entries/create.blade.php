@extends('layouts.app')

@section('title', 'Log time — Ticket #'.$ticket->id)
@section('page_title', 'Log time')

@section('content')
    <div class="max-w-lg">
        <p class="mb-5 text-sm">
            <a href="{{ route('admin.time-entries.index', $ticket) }}" class="font-medium text-indigo-600 hover:underline">← Time entries</a>
            — Ticket #{{ $ticket->id }}: {{ Str::limit($ticket->subject, 50) }}
        </p>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.time-entries.store', $ticket) }}" class="space-y-5">
                @csrf
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-slate-700 mb-1.5">Duration (minutes)</label>
                    <input id="duration_minutes" name="duration_minutes" type="number" min="1" max="1440" value="{{ old('duration_minutes') }}" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('duration_minutes') border-red-400 @enderror">
                    @error('duration_minutes') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">Description <span class="text-slate-400">(optional)</span></label>
                    <textarea id="description" name="description" rows="3" placeholder="What did you work on?"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="started_at" class="block text-sm font-medium text-slate-700 mb-1.5">Started at <span class="text-slate-400">(optional)</span></label>
                        <input id="started_at" name="started_at" type="datetime-local" value="{{ old('started_at') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        @error('started_at') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="stopped_at" class="block text-sm font-medium text-slate-700 mb-1.5">Stopped at <span class="text-slate-400">(optional)</span></label>
                        <input id="stopped_at" name="stopped_at" type="datetime-local" value="{{ old('stopped_at') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        @error('stopped_at') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">Save entry</button>
                    <a href="{{ route('admin.time-entries.index', $ticket) }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
