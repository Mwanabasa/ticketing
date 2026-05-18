<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\KnowledgeBaseArticle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = KnowledgeBaseArticle::query()
            ->where('is_published', true)
            ->with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('q')) {
            $needle = $request->string('q')->trim();
            $query->where(function ($q) use ($needle): void {
                $q->where('title', 'like', '%'.$needle.'%')
                    ->orWhere('content', 'like', '%'.$needle.'%');
            });
        }

        // JSON response for live KB search on ticket create form
        if ($request->input('_format') === 'json') {
            return response()->json(
                $query->limit(4)->get(['id', 'title', 'slug'])
            );
        }

        $articles   = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::query()->orderBy('name')->get();

        return view('knowledge-base.index', compact('articles', 'categories'));
    }

    public function show(KnowledgeBaseArticle $article): View
    {
        if (! $article->is_published) {
            abort(404);
        }

        $article->incrementViews();

        $relatedArticles = KnowledgeBaseArticle::query()
            ->where('id', '!=', $article->id)
            ->where('category_id', $article->category_id)
            ->where('is_published', true)
            ->latest()
            ->limit(5)
            ->get();

        return view('knowledge-base.show', compact('article', 'relatedArticles'));
    }
}
