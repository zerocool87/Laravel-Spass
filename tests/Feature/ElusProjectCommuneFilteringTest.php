<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElusProjectCommuneFilteringTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure élus only see projects from their commune.
     */
    public function test_elu_only_sees_projects_from_his_commune_in_projects_index(): void
    {
        $elu = User::factory()->create([
            'is_elu' => true,
            'commune' => 'Limoges',
        ]);

        $visibleProject = $this->createProject([
            'title' => 'Travaux visibles',
            'commune' => 'Limoges',
        ]);

        $hiddenProject = $this->createProject([
            'title' => 'Travaux cachés',
            'commune' => 'Panazol',
        ]);

        $response = $this->actingAs($elu)->get(route('elus.projects.index'));

        $response->assertOk();
        $response->assertSee($visibleProject->title);
        $response->assertDontSee($hiddenProject->title);
    }

    /**
     * Ensure élus cannot open a project from another commune.
     */
    public function test_elu_cannot_open_project_from_another_commune(): void
    {
        $elu = User::factory()->create([
            'is_elu' => true,
            'commune' => 'Limoges',
        ]);

        $otherCommuneProject = $this->createProject([
            'title' => 'Travaux autre commune',
            'commune' => 'Panazol',
        ]);

        $response = $this->actingAs($elu)->get(route('elus.projects.show', $otherCommuneProject));

        $response->assertForbidden();
    }

    /**
     * Ensure admins can assign a commune when creating a project.
     */
    public function test_admin_can_assign_commune_when_creating_project(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('elus.projects.store'), [
            'title' => 'Nouveau projet communal',
            'description' => 'Description',
            'type' => 'infrastructure',
            'status' => 'planifie',
            'commune' => 'Limoges',
            'budget' => 12000,
        ]);

        $response->assertRedirect(route('elus.projects.index'));
        $this->assertDatabaseHas('projects', [
            'title' => 'Nouveau projet communal',
            'commune' => 'Limoges',
        ]);
    }

    private function createProject(array $overrides = []): Project
    {
        return Project::query()->create(array_merge([
            'title' => 'Projet test',
            'description' => 'Description test',
            'type' => 'infrastructure',
            'status' => 'planifie',
        ], $overrides));
    }
}
