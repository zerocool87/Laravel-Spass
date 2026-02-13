<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use App\Notifications\DocumentActionNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminDocumentNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigned_users_receive_notification_on_create(): void
    {
        Notification::fake();
        Storage::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $assigned = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.documents.store'), [
            'title' => 'Budget 2026',
            'description' => 'Document budgetaire',
            'file' => UploadedFile::fake()->create('budget.pdf', 200, 'application/pdf'),
            'assigned_users' => [$assigned->id],
        ]);

        $response->assertRedirect(route('admin.documents.index'));

        Notification::assertSentTo($assigned, DocumentActionNotification::class);
    }

    public function test_assigned_users_receive_notification_on_update(): void
    {
        Notification::fake();
        Storage::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $assigned = User::factory()->create();

        $document = Document::create([
            'title' => 'Budget 2026',
            'description' => 'Document budgetaire',
            'path' => 'documents/budget.pdf',
            'original_name' => 'budget.pdf',
            'created_by' => $admin->id,
            'visible_to_all' => false,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.documents.update', $document), [
            'title' => 'Budget 2026 v2',
            'description' => 'Document budgetaire',
            'assigned_users' => [$assigned->id],
        ]);

        $response->assertRedirect(route('admin.documents.index'));

        Notification::assertSentTo($assigned, DocumentActionNotification::class);
    }
}
