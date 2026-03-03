<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Instance;

class ElusInstanceHeaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_links_absent_for_admin()
    {
        $instance = Instance::factory()->create();
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        $response = $this->actingAs($admin)->get(route('elus.instances.show', $instance));
        $response->assertStatus(200);

        $content = $response->getContent();

        // header used to contain a plus-prefixed link; ensure that exact header variant is absent
        $this->assertDoesNotMatchRegularExpression('/<a[^>]*>\s*\+\s*Planifier une réunion\s*<\/a>/i', $content);
        // header had exact anchor with text 'Modifier' — ensure no anchor exists with only 'Modifier' text
        $this->assertDoesNotMatchRegularExpression('/<a[^>]*>\s*Modifier\s*<\/a>/i', $content);
    }

    public function test_header_links_absent_for_regular_user()
    {
        $instance = Instance::factory()->create();
        $user = User::factory()->create(['is_admin' => false, 'is_elu' => true]);

        $response = $this->actingAs($user)->get(route('elus.instances.show', $instance));
        $response->assertStatus(200);

        $content = $response->getContent();

        $this->assertDoesNotMatchRegularExpression('/<a[^>]*>\s*\+\s*Planifier une réunion\s*<\/a>/i', $content);
        $this->assertDoesNotMatchRegularExpression('/<a[^>]*>\s*Modifier\s*<\/a>/i', $content);
    }
}
