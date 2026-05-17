@extends('layouts.app')

@section('title', 'New ticket')
@section('page_title', 'New ticket')

@section('content')
    <div class="max-w-2xl">

        @if ($templates->isNotEmpty())
            <div class="mb-6 rounded-2xl border border-indigo-100 bg-indigo-50 p-5">
                <p class="text-sm font-semibold text-indigo-900 mb-3">
                    <svg class="inline w-4 h-4 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Use a template to pre-fill this form
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($templates as $template)
                        <button type="button"
                            onclick="applyTemplate({{ $template->id }})"
                            class="template-btn rounded-xl border border-indigo-200 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 shadow-sm hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition">
                            {{ $template->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
            <form method="POST" action="{{ route('student.tickets.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1.5">Category</label>
                    <select id="category_id" name="category_id" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Select a category</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="priority" class="block text-sm font-medium text-slate-700 mb-1.5">Priority</label>
                    <select id="priority" name="priority" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        @foreach (\App\Enums\TicketPriority::cases() as $p)
                            <option value="{{ $p->value }}" @selected(old('priority') == $p->value)>{{ $p->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="subject" class="block text-sm font-medium text-slate-700 mb-1.5">Subject</label>
                    <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required maxlength="255"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    @error('subject') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                    <textarea id="description" name="description" rows="6" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="attachment" class="block text-sm font-medium text-slate-700 mb-1.5">Screenshot <span class="text-slate-400">(optional)</span></label>
                    <input id="attachment" name="attachment" type="file" accept="image/*,application/pdf"
                        class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 transition">
                    <p class="mt-1.5 text-xs text-slate-400">PNG, JPG, PDF. Max 4 MB.</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                        Submit ticket
                    </button>
                    <a href="{{ route('student.tickets.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if ($templates->isNotEmpty())
        @push('head')
        <script>
            const templates = @json($templates->keyBy('id'));

            function applyTemplate(id) {
                const t = templates[id];
                if (!t) return;

                document.getElementById('subject').value = t.subject;
                document.getElementById('description').value = t.description;

                if (t.category_id) {
                    document.getElementById('category_id').value = t.category_id;
                }

                // Highlight active template button
                document.querySelectorAll('.template-btn').forEach(btn => {
                    btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
                    btn.classList.add('bg-white', 'text-indigo-700', 'border-indigo-200');
                });
                event.target.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                event.target.classList.remove('bg-white', 'text-indigo-700', 'border-indigo-200');

                document.getElementById('subject').focus();
            }
        </script>
        @endpush
    @endif
@endsection
