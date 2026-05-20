@extends('layouts.app')

@section('title', 'New Ticket')
@section('page_title', 'New Ticket')
@section('page_subtitle', 'Describe your issue and we\'ll get back to you')

@section('content')
<div class="max-w-2xl">

    {{-- Templates --}}
    @if ($templates->isNotEmpty())
        <div class="mb-5 bg-indigo-50 border border-indigo-100 rounded-2xl p-4">
            <p class="text-xs font-semibold text-indigo-700 uppercase tracking-widest mb-3">Quick templates</p>
            <div class="flex flex-wrap gap-2">
                @foreach ($templates as $template)
                    <button type="button" onclick="applyTemplate({{ $template->id }}, this)"
                            class="template-btn rounded-lg border border-indigo-200 bg-white px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition">
                        {{ $template->name }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Form card --}}
    <div class="bg-[#f4f5fb] rounded-2xl border border-slate-200 shadow-sm">
        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="font-semibold text-slate-900">Ticket details</h2>
            <p class="text-xs text-slate-400 mt-0.5">Fill in the details below. The more info you provide, the faster we can help.</p>
        </div>

        <form method="POST" action="{{ route('student.tickets.store') }}" enctype="multipart/form-data" class="p-6 space-y-5" data-loading>
            @csrf

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1.5">Category</label>
                    <select id="category_id" name="category_id" required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 transition @error('category_id') border-red-400 @enderror">
                        <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Select a category</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-slate-700 mb-1.5">Priority</label>
                    <select id="priority" name="priority" required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 transition">
                        @foreach (\App\Enums\TicketPriority::cases() as $p)
                            <option value="{{ $p->value }}" @selected(old('priority') == $p->value)>{{ $p->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="subject" class="block text-sm font-medium text-slate-700 mb-1.5">Subject</label>
                <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required maxlength="255"
                    placeholder="Brief summary of your issue"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 transition @error('subject') border-red-400 @enderror"
                    oninput="searchKB(this.value)">
                @error('subject') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror

                {{-- KB suggestions --}}
                <div id="kb-suggestions" class="hidden mt-2 rounded-xl border border-indigo-100 bg-indigo-50 p-3 space-y-1.5">
                    <p class="text-xs font-semibold text-indigo-700 mb-1.5">💡 Before submitting — check these articles:</p>
                    <div id="kb-results"></div>
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                <textarea id="description" name="description" rows="6" required
                    placeholder="Describe your issue in detail — what happened, what you expected, any error messages…"
                    data-maxlength="5000"
                    class="w-full rounded-lg border border-slate-200 bg-[#eceef6] px-3 py-2.5 text-sm focus:border-indigo-400 focus:bg-[#f4f5fb] focus:outline-none focus:ring-2 focus:ring-indigo-100 transition resize-none @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="attachment" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Screenshot <span class="text-slate-400 font-normal">(optional)</span>
                </label>
                <label for="attachment"
                       class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-8 cursor-pointer
                              hover:border-indigo-400 hover:bg-indigo-50/30 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-sm group-hover:border-indigo-200 group-hover:shadow-indigo-100 transition-all">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-slate-700 group-hover:text-indigo-600 transition-colors">Click to upload a screenshot</p>
                        <p class="text-xs text-slate-400 mt-0.5">PNG, JPG, PDF &middot; Max 4 MB</p>
                    </div>
                    <input id="attachment" name="attachment" type="file" accept="image/*,application/pdf" class="sr-only">
                </label>
                <p id="file-name" class="mt-2 text-xs text-indigo-600 font-medium hidden"></p>
            </div>

            <div class="flex gap-3 pt-2 border-t border-slate-100">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold text-white shadow-md transition-all hover:-translate-y-0.5 hover:shadow-lg"
                        style="background: linear-gradient(135deg, #4f46e5, #7c3aed); box-shadow: 0 4px 14px rgba(79,70,229,0.35);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Submit ticket
                </button>
                <a href="{{ route('student.tickets.index') }}"
                   class="inline-flex items-center rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@if ($templates->isNotEmpty())
    @push('scripts')
    <script>
    const templates = @json($templates->keyBy('id'));
    function applyTemplate(id, btn) {
        const t = templates[id];
        if (!t) return;
        document.getElementById('subject').value = t.subject;
        document.getElementById('description').value = t.description;
        if (t.category_id) document.getElementById('category_id').value = t.category_id;
        document.querySelectorAll('.template-btn').forEach(b => {
            b.classList.remove('bg-indigo-600','text-white','border-indigo-600');
            b.classList.add('bg-white','text-indigo-700','border-indigo-200');
        });
        btn.classList.add('bg-indigo-600','text-white','border-indigo-600');
        btn.classList.remove('bg-white','text-indigo-700','border-indigo-200');
        document.getElementById('subject').focus();
        document.getElementById('subject').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    </script>
    @endpush
@endif

@push('scripts')
<script>
let kbTimer;
function searchKB(query) {
    clearTimeout(kbTimer);
    const box = document.getElementById('kb-suggestions');
    const results = document.getElementById('kb-results');
    if (query.length < 4) { box.classList.add('hidden'); return; }
    kbTimer = setTimeout(() => {
        fetch('/api/knowledge-base/search?q=' + encodeURIComponent(query))
            .then(r => r.json())
            .then(data => {
                if (!data.length) { box.classList.add('hidden'); return; }
                results.innerHTML = data.map(a =>
                    `<a href="/knowledge-base/${a.slug}" target="_blank"
                        class="flex items-center justify-between rounded-lg bg-white border border-indigo-100 px-3 py-2 text-xs hover:border-indigo-300 transition">
                        <span class="font-medium text-slate-800">${a.title}</span>
                        <span class="text-indigo-400 shrink-0">→</span>
                    </a>`
                ).join('');
                box.classList.remove('hidden');
            }).catch(() => box.classList.add('hidden'));
    }, 400);
}
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('attachment');
    const label = document.getElementById('file-name');
    if (input && label) {
        input.addEventListener('change', () => {
            if (input.files.length) {
                label.textContent = '✔ ' + input.files[0].name;
                label.classList.remove('hidden');
            } else {
                label.classList.add('hidden');
            }
        });
    }
});
</script>
@endpush
@endsection
