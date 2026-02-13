<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRecentDocumentsNewBadgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_new_badge_for_recent_documents(): void
    {
        $user = User::factory()->create(['is_elu' => true]);

        $recent = Document::create([
            'title' => 'Budget 2026',
            'path' => 'documents/budget.pdf',
            'created_by' => $user->id,
            'visible_to_all' => true,
        ]);
        $recent->forceFill([
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(3),
        ])->save();

        $old = Document::create([
            'title' => 'Budget 2020',
            'path' => 'documents/budget-2020.pdf',
            'created_by' => $user->id,
            'visible_to_all' => true,
        ]);
        $old->forceFill([
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ])->save();

        $response = $this->actingAs($user)->get(route('elus.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Budget 2026');
        $response->assertSee('Budget 2020');
        $response->assertSee('new');
    }

    public function test_dashboard_does_not_show_new_badge_for_old_documents(): void
    {
        $user = User::factory()->create(['is_elu' => true]);

        $old = Document::create([
            'title' => 'Budget 2019',
            'path' => 'documents/budget-2019.pdf',
            'created_by' => $user->id,
            'visible_to_all' => true,
        ]);
        $old->forceFill([
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ])->save();

        $response = $this->actingAs($user)->get(route('elus.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Budget 2019');
        $response->assertDontSee('new');
    }
}
