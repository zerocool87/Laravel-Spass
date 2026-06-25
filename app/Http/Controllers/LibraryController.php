<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LibraryController extends Controller
{
    public function index(Request $request): View
    {
        $documents = Document::accessibleTo($request->user())->latest()->get();

        $documentsByCategory = $documents->groupBy(
            fn (Document $d) => $d->category ?: __('Non catégorisé')
        );

        $allCategories = $documentsByCategory->keys()->all();

        return view('library.index', compact('documentsByCategory', 'allCategories'));
    }
}
