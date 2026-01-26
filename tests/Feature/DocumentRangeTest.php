<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentRangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_embed_returns_partial_content_for_range()
    {
        Storage::fake('local');
        $content = '0123456789'; // 10 bytes
        $path = 'documents/range.pdf';
        Storage::disk('local')->put($path, $content);

        $user = User::factory()->create();
        $doc = Document::create([
            'title' => 'Range test',
            'path' => $path,
            'original_name' => 'range.pdf',
            'created_by' => $user->id,
            'visible_to_all' => true,
        ]);

        $response = $this->actingAs($user)->get(route('documents.embed', $doc), ['Range' => 'bytes=2-5']);
        $response->assertStatus(206);
        $this->assertEquals('bytes 2-5/10', $response->headers->get('Content-Range'));
        $this->assertEquals('4', $response->headers->get('Content-Length'));
        $this->assertEquals('2345', $response->getContent());
        $this->assertEquals('bytes', $response->headers->get('Accept-Ranges'));
    }

    public function test_embed_returns_416_for_invalid_range()
    {
        Storage::fake('local');
        $content = '0123456789'; // 10 bytes
        $path = 'documents/range2.pdf';
        Storage::disk('local')->put($path, $content);

        $user = User::factory()->create();
        $doc = Document::create([
            'title' => 'Range test 2',
            'path' => $path,
            'original_name' => 'range2.pdf',
            'created_by' => $user->id,
            'visible_to_all' => true,
        ]);

        $response = $this->actingAs($user)->get(route('documents.embed', $doc), ['Range' => 'bytes=100-200']);
        $response->assertStatus(416);
        $this->assertEquals('bytes */10', $response->headers->get('Content-Range'));
    }

    public function test_embed_returns_full_when_no_range_provided()
    {
        Storage::fake('local');
        $content = 'HelloWorld';
        $path = 'documents/full.pdf';
        Storage::disk('local')->put($path, $content);

        $user = User::factory()->create();
        $doc = Document::create([
            'title' => 'Full test',
            'path' => $path,
            'original_name' => 'full.pdf',
            'created_by' => $user->id,
            'visible_to_all' => true,
        ]);

        $response = $this->actingAs($user)->get(route('documents.embed', $doc));
        $response->assertStatus(200);
        $this->assertEquals($content, $response->getContent());
        $this->assertEquals('bytes', $response->headers->get('Accept-Ranges'));
    }
}
