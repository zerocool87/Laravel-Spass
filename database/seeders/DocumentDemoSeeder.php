<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DocumentDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $test = User::where('email', 'test@example.com')->first();

        // ensure files exist
        Storage::disk('local')->put('documents/demo_public.txt', 'Demo public document content');
        Storage::disk('local')->put('documents/demo_restricted.txt', 'Demo restricted document content');

        $doc1 = Document::create([
            'title' => 'Demo Public Document',
            'description' => 'Document public pour tous',
            'path' => 'documents/demo_public.txt',
            'original_name' => 'demo_public.txt',
            'created_by' => $admin?->id,
            'visible_to_all' => true,
        ]);

        $doc2 = Document::create([
            'title' => 'Demo Restricted Document',
            'description' => 'Document attribué à test@example.com',
            'path' => 'documents/demo_restricted.txt',
            'original_name' => 'demo_restricted.txt',
            'created_by' => $admin?->id,
            'visible_to_all' => false,
        ]);

        if ($test && $doc2) {
            $doc2->users()->sync([$test->id]);
        }
    }
}
