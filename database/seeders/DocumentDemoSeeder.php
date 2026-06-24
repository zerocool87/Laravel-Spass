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
        $elus = User::where('is_elu', true)->get();

        Storage::disk('local')->put('documents/demo_public.txt', 'Demo public document content');
        Storage::disk('local')->put('documents/demo_restricted.txt', 'Demo restricted document content');

        Document::create([
            'title' => 'Demo Public Document',
            'description' => 'Document public pour tous',
            'path' => 'documents/demo_public.txt',
            'original_name' => 'demo_public.txt',
            'created_by' => $admin?->id,
            'visible_to_all' => true,
        ]);

        $doc2 = Document::create([
            'title' => 'Demo Restricted Document',
            'description' => 'Document restreint pour les élus',
            'path' => 'documents/demo_restricted.txt',
            'original_name' => 'demo_restricted.txt',
            'created_by' => $admin?->id,
            'visible_to_all' => false,
        ]);

        if ($doc2 && $elus->isNotEmpty()) {
            $doc2->users()->sync($elus->pluck('id')->toArray());
        }
    }
}
