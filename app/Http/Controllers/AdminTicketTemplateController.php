<?php

namespace App\Http\Controllers;

use App\Models\TicketTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTicketTemplateController extends Controller
{
    public function index(): View
    {
        $templates = TicketTemplate::query()
            ->with('category')
            ->latest()
            ->paginate(15);

        return view('admin.templates.index', compact('templates'));
    }

    public function create(): View
    {
        $categories = \App\Models\Category::query()->orderBy('name')->get();
        return view('admin.templates.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'is_active' => ['boolean'],
        ]);

        TicketTemplate::query()->create($validated);

        return redirect()->route('admin.templates.index')->with('status', 'Template created.');
    }

    public function edit(TicketTemplate $template): View
    {
        $categories = \App\Models\Category::query()->orderBy('name')->get();
        return view('admin.templates.edit', compact('template', 'categories'));
    }

    public function update(Request $request, TicketTemplate $template): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'is_active' => ['boolean'],
        ]);

        $template->update($validated);

        return redirect()->route('admin.templates.index')->with('status', 'Template updated.');
    }

    public function destroy(TicketTemplate $template): RedirectResponse
    {
        $template->delete();

        return redirect()->route('admin.templates.index')->with('status', 'Template deleted.');
    }
}
