<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminUsersCommuneTest extends TestCase
{
    public function test_communes_configuration_is_loaded()
    {
        $communes = config('options.communes_haute_vienne', []);

        // Should have at least some communes from Haute-Vienne
        $this->assertGreaterThan(100, count($communes));
        $this->assertContains('Limoges', $communes);
        $this->assertContains('Saint-Junien', $communes);
    }
}
