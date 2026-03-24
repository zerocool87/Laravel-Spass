<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\View\View;

class LibraryController extends Controller
{
    public function index(): View
    {
        $documents = Document::latest()->get();

        $documentsByCategory = $documents->groupBy(
            fn (Document $d) => $d->category ?: 'Uncategorized'
        );

        $allCategories = $documentsByCategory->keys()->all();

        return view('library.index', compact('documentsByCategory', 'allCategories'));
    }
}
