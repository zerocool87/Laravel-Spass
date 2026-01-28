<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $user = auth()->user();
        $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();

        // Base query: apply visibility rules
        $base = Document::query();
        if (! $isAdmin) {
            $base = $base->visibleToUser($user?->id);
        }

        // Filtered query (used for paginated list)
        $filtered = clone $base;
        if ($category && $category !== 'all') {
            if ($category === 'Uncategorized') {
                $filtered->where(function ($q) {
                    $q->whereNull('category')->orWhere('category', '');
                });
            } else {
                $filtered->where('category', $category);
            }
        }

        // Paginated documents (for the flat list when filters are active)
        $documents = (clone $filtered)->latest()->paginate(15);

        // When showing all categories, we need grouped documents. Load once.
        $showAll = ! $category || $category === 'all';
        $documentsByCategory = collect();
        if ($showAll) {
            $allDocuments = (clone $base)->latest()->get();
            $documentsByCategory = $allDocuments->groupBy(function ($doc) {
                return $doc->category ?: 'Uncategorized';
            });
        }

        // Compute category counts in a single query to avoid N+1
        $counts = (clone $base)
            ->selectRaw("COALESCE(NULLIF(category, ''), 'Uncategorized') as category, count(*) as cnt")
            ->groupByRaw("COALESCE(NULLIF(category, ''), 'Uncategorized')")
            ->pluck('cnt', 'category')
            ->toArray();

        $allCategories = config('documents.categories', []);
        $categoryCounts = [];
        foreach ($allCategories as $cat) {
            if (isset($counts[$cat]) && $counts[$cat] > 0) {
                $categoryCounts[$cat] = $counts[$cat];
            }
        }
        if (isset($counts['Uncategorized']) && $counts['Uncategorized'] > 0) {
            $categoryCounts['Uncategorized'] = $counts['Uncategorized'];
        }

        return view('library.index', compact('documents', 'category', 'categoryCounts', 'documentsByCategory'));
    }
}
