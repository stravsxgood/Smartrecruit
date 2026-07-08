<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    public function index(): Response
    {
        $news = News::where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with('author')
            ->latest('published_at')
            ->paginate(12);

        $latestNews = News::where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->first();

        $popularNews = News::where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('News/Index', [
            'news' => $news,
            'latestNews' => $latestNews,
            'popularNews' => $popularNews,
        ]);
    }

    public function show(string $slug): Response
    {
        $news = News::where('slug', $slug)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with('author')
            ->firstOrFail();

        // Increment views
        $news->increment('views');

        // Get related news
        $relatedNews = News::where('is_published', true)
            ->where('id', '!=', $news->id)
            ->where('category', $news->category)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->limit(4)
            ->get();

        return Inertia::render('News/Show', [
            'news' => $news,
            'relatedNews' => $relatedNews,
        ]);
    }
}
