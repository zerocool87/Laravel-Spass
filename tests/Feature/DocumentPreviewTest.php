<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_info_returns_document_metadata()
    {
        Storage::fake('local');
        $path = 'documents/test.pdf';
        Storage::disk('local')->put($path, '%PDF-1.4 test');

        $user = User::factory()->create();
        $doc = Document::create([
            'title' => 'PDF Test',
            'path' => $path,
            'original_name' => 'test.pdf',
            'created_by' => $user->id,
            'visible_to_all' => true,
        ]);

        $this->actingAs($user)
            ->get(route('documents.info', $doc))
            ->assertStatus(200)
            ->assertJsonStructure(['mime', 'previewable', 'embed_url', 'download_url']);
    }

    public function test_embed_returns_content()
    {
        Storage::fake('local');
        $content = 'Dummy PDF content';
        $path = 'documents/test2.pdf';
        Storage::disk('local')->put($path, $content);

        $user = User::factory()->create();
        $doc = Document::create([
            'title' => 'PDF Test2',
            'path' => $path,
            'original_name' => 'test2.pdf',
            'created_by' => $user->id,
            'visible_to_all' => true,
        ]);

        $response = $this->actingAs($user)->get(route('documents.embed', $doc));
        $response->assertStatus(200);
        $this->assertEquals($content, $response->getContent());
        $this->assertEquals('bytes', $response->headers->get('Accept-Ranges'));
    }

    public function test_preview_modal_shows_not_previewable_message_in_markup()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('library.index'))
            ->assertStatus(200)
            ->assertSee(__('Preview not available for this file type.'));
    }

    public function test_info_returns_previewable_false_for_non_previewable_file()
    {
        Storage::fake('local');
        $path = 'documents/test.bin';
        Storage::disk('local')->put($path, "\x00\x01\x02\x03");

        $user = User::factory()->create();
        $doc = Document::create([
            'title' => 'Binary Test',
            'path' => $path,
            'original_name' => 'test.bin',
            'created_by' => $user->id,
            'visible_to_all' => true,
        ]);

        $this->actingAs($user)
            ->get(route('documents.info', $doc))
            ->assertStatus(200)
            ->assertJsonFragment(['previewable' => false]);
    }
}
