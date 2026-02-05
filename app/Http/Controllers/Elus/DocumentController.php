<?php

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Elus\Concerns\FiltersDocuments;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    use FiltersDocuments;

    /**
     * Display a listing of the documents.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = $this->getUserAccessibleDocuments($user);

        // Filter by category
        if ($request->filled('category')) {
            if ($request->category === 'uncategorized') {
                $query->whereNull('category');
            } else {
                $query->where('category', $request->category);
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $documents = $query->latest()->get();
        $documentsByCategory = $documents->groupBy(function ($d) {
            return $d->category ?: 'Non catégorisé';
        });

        $categories = Document::query()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort();

        return view('elus.documents.index', compact('documentsByCategory', 'categories'));
    }
}
