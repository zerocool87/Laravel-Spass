<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class LibraryController
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $user = auth()->user();

        // Si admin, accès à tous les documents
        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $query = Document::query();
        } else {
            $query = Document::visibleToUser($user?->id);
        }

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

        // On récupère tous les documents (non paginés) pour grouper par catégorie
        $allDocuments = $query->latest()->get();
        $documentsByCategory = $allDocuments->groupBy(function($doc) {
            return $doc->category ?: 'Uncategorized';
        });

        // Pour compatibilité, on garde la pagination sur la liste plate
        $documents = $query->latest()->paginate(15);

        // Get all categories with document counts
        $allCategories = config('documents.categories', []);
        $categoryCounts = [];

        foreach ($allCategories as $cat) {
            if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
                $count = Document::where('category', $cat)->count();
            } else {
                $count = Document::visibleToUser($user?->id)->where('category', $cat)->count();
            }
            if ($count > 0) {
                $categoryCounts[$cat] = $count;
            }
        }

        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $uncatCount = Document::where(function ($q) {
                $q->whereNull('category')->orWhere('category', '');
            })->count();
        } else {
            $uncatCount = Document::visibleToUser($user?->id)->where(function ($q) {
                $q->whereNull('category')->orWhere('category', '');
            })->count();
        }

        if ($uncatCount > 0) {
            $categoryCounts['Uncategorized'] = $uncatCount;
        }

        return view('library.index', compact('documents', 'category', 'categoryCounts', 'documentsByCategory'));
    }
}
