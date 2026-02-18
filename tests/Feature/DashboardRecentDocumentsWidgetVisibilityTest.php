<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRecentDocumentsWidgetVisibilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure the recent documents widget is displayed even without documents.
     */
    public function test_dashboard_displays_recent_documents_widget_when_no_documents_exist(): void
    {
        $user = User::factory()->create(['is_elu' => true]);

        $response = $this->actingAs($user)->get(route('elus.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Documents récents');
        $response->assertSee('Aucun document récent');
    }
}
