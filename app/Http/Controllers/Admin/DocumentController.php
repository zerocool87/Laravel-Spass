<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','can:admin']);
    }

    public function index(): View
    {
        $documents = Document::with('creator')->orderBy('created_at','desc')->paginate(20);

        return view('admin.documents.index', compact('documents'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();
        return view('admin.documents.create', compact('users'));
    }

    public function store(DocumentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $file = $request->file('file');
        $path = $file->store('documents');

        $document = Document::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'created_by' => $request->user()->id,
            'visible_to_all' => boolval($data['visible_to_all']),
        ]);

        if (!$document->visible_to_all && !empty($data['assigned_users'])) {
            $document->users()->sync($data['assigned_users']);
        }

        return Redirect::route('admin.documents.index')->with('success', 'Document uploaded.');
    }

    public function edit(Document $document): View
    {
        $users = User::orderBy('name')->get();
        $assigned = $document->users()->pluck('users.id')->toArray();
        return view('admin.documents.edit', compact('document','users','assigned'));
    }

    public function update(DocumentRequest $request, Document $document): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            // delete old
            Storage::delete($document->path);
            $file = $request->file('file');
            $path = $file->store('documents');
            $document->path = $path;
            $document->original_name = $file->getClientOriginalName();
        }

        $document->title = $data['title'];
        $document->description = $data['description'] ?? null;
        $document->visible_to_all = boolval($data['visible_to_all']);
        $document->save();

        if (!$document->visible_to_all && !empty($data['assigned_users'])) {
            $document->users()->sync($data['assigned_users']);
        } else {
            $document->users()->detach();
        }

        return Redirect::route('admin.documents.index')->with('success','Document updated.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        Storage::delete($document->path);
        $document->delete();

        return Redirect::route('admin.documents.index')->with('success','Document deleted.');
    }

    public function download(Document $document)
    {
        // Authorization: must be visible to all or assigned to user or admin
        $user = auth()->user();
        if (!$document->visible_to_all && !$user->isAdmin() && !$document->users()->where('user_id',$user->id)->exists()){
            abort(403);
        }

        return Storage::download($document->path, $document->original_name ?? basename($document->path));
    }
}
