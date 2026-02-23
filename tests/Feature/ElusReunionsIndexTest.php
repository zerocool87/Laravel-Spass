<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElusReunionsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_elus_reunions_index_does_not_show_new_reunion_link(): void
    {
        $adminElu = User::factory()->create([
            'is_admin' => true,
            'is_elu' => true,
        ]);

        $response = $this->actingAs($adminElu)->get(route('elus.reunions.index'));

        $response->assertStatus(200);
        $response->assertDontSee('href="'.route('elus.reunions.create').'"', false);
        $response->assertDontSee('>+ '.__('Nouvelle réunion').'<', false);
    }
}
