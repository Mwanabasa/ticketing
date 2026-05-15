@extends('layouts.app')

@section('title', 'New ticket')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900">Create support ticket</h1>
    <p class="mt-1 text-slate-600">Describe your issue and optionally attach a screenshot.</p>

    <form method="POST" action="{{ route('student.tickets.store') }}" enctype="multipart/form-data" class="mt-8 max-w-xl space-y-6">
        @csrf
        <div>
            <label for="category_id" class="block text-sm font-medium text-slate-700">Category</label>
            <select id="category_id" name="category_id" required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Select a category</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="subject" class="block text-sm font-medium text-slate-700">Subject</label>
            <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required maxlength="255"
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
            <textarea id="description" name="description" rows="6" required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">{{ old('description') }}</textarea>
        </div>
        <div>
            <label for="attachment" class="block text-sm font-medium text-slate-700">Screenshot (optional)</label>
            <input id="attachment" name="attachment" type="file" accept="image/*"
                class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-slate-800 hover:file:bg-slate-200">
            <p class="mt-1 text-xs text-slate-500">PNG, JPG, GIF or WebP. Max 4 MB.</p>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Submit ticket
            </button>
            <a href="{{ route('student.tickets.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
@endsection
