<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class LibraryController
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $userId = auth()->id();

        $query = Document::visibleToUser($userId);

        // Filter by category if specified
        if ($category && $category !== 'all') {
            if ($category === 'Uncategorized') {
                $query->where(function ($q) {
                    $q->whereNull('category')->orWhere('category', '');
                });
            } else {
                $query->where('category', $category);
            }
        }

        $documents = $query->latest()->paginate(15);

        // Get all categories with document counts
        $allCategories = config('documents.categories', []);
        $categoryCounts = [];

        foreach ($allCategories as $cat) {
            $count = Document::visibleToUser($userId)->where('category', $cat)->count();
            if ($count > 0) {
                $categoryCounts[$cat] = $count;
            }
        }

        $uncatCount = Document::visibleToUser($userId)->where(function ($q) {
            $q->whereNull('category')->orWhere('category', '');
        })->count();

        if ($uncatCount > 0) {
            $categoryCounts['Uncategorized'] = $uncatCount;
        }

        return view('library.index', compact('documents', 'category', 'categoryCounts'));
    }
}
