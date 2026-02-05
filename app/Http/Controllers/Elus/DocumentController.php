<?php

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    /**
     * Display a listing of the documents.
     */
    public function index(Request $request): View
    {
        $query = Document::query()
            ->where(function ($q) use ($request) {
                $user = $request->user();
                $q->where('visible_to_all', true)
                    ->orWhere('created_by', $user->id)
                    ->orWhereHas('users', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
            });

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
