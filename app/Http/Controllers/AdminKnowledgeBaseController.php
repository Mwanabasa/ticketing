<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\KnowledgeBaseArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminKnowledgeBaseController extends Controller
{
    public function index(): View
    {
        $articles = KnowledgeBaseArticle::query()
            ->with('category')
            ->latest()
            ->paginate(15);

        return view('admin.knowledge-base.index', compact('articles'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.knowledge-base.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'  => ['nullable', 'exists:categories,id'],
            'title'        => ['required', 'string', 'max:255'],
            'content'      => ['required', 'string'],
            'is_published' => ['boolean'],
        ]);

        KnowledgeBaseArticle::query()->create([
            'category_id'  => $validated['category_id'] ?? null,
            'title'        => $validated['title'],
            'slug'         => Str::slug($validated['title']) . '-' . Str::random(5),
            'content'      => $validated['content'],
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.knowledge-base.index')->with('status', 'Article created.');
    }

    public function edit(KnowledgeBaseArticle $article): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.knowledge-base.edit', compact('article', 'categories'));
    }

    public function update(Request $request, KnowledgeBaseArticle $article): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'  => ['nullable', 'exists:categories,id'],
            'title'        => ['required', 'string', 'max:255'],
            'content'      => ['required', 'string'],
            'is_published' => ['boolean'],
        ]);

        $article->update([
            'category_id'  => $validated['category_id'] ?? null,
            'title'        => $validated['title'],
            'content'      => $validated['content'],
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.knowledge-base.index')->with('status', 'Article updated.');
    }

    public function destroy(KnowledgeBaseArticle $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('admin.knowledge-base.index')->with('status', 'Article deleted.');
    }
}
