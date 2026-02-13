<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LibraryController extends Controller
{
    public function index(): View
    {
        return view('library.index');
    }
}
