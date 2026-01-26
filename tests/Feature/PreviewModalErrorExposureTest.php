<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreviewModalErrorExposureTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_modal_does_not_expose_pdf_error_message()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Le visualiseur PDF n\'a pas pu être initialisé', false);
        $response->assertDontSee('PDF viewer failed to initialize', false);
    }
}
