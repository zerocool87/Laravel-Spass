<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Elus\Concerns\FiltersDocuments;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LibraryController extends Controller
{
    use FiltersDocuments;

    public function index(Request $request): View
    {
        $user = $request->user();

        $documents = $user && $user->isAdmin()
            ? Document::latest()->get()
            : $this->getUserAccessibleDocuments($user)->latest()->get();

        $documentsByCategory = $documents->groupBy(
            fn (Document $d) => $d->category ?: 'Uncategorized'
        );

        $allCategories = $documentsByCategory->keys()->all();

        return view('library.index', compact('documentsByCategory', 'allCategories'));
    }
}
