<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Tests for Full HD (1920x1080) screen compatibility
 */
class FullHDScreenTest extends DuskTestCase
{
    /**
     * Test dashboard layout on Full HD screen
     *
     * @return void
     */
    public function test_dashboard_full_hd_layout()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertSee('Espace Élus')
                ->assertSee('Gouvernance et projets territoriaux')
                ->assertVisible('#app')
                ->assertVisible('.main-content')
                ->assertVisible('.glass')
                ->assertVisible('.btn-primary')
                ->assertVisible('.cyber-table');
        });
    }

    /**
     * Test statistics cards are properly spaced on Full HD
     *
     * @return void
     */
    public function test_statistics_cards_spacing()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.grid')
                ->assertVisible('.bg-\[\#faa21b\]')
                ->assertVisible('p:text-3xl');
        });
    }

    /**
     * Test main content grid layout on Full HD
     *
     * @return void
     */
    public function test_main_content_grid_layout()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.grid-cols-1')
                ->assertVisible('.lg\:grid-cols-2')
                ->assertVisible('.gap-8');
        });
    }

    /**
     * Test widget containers have proper padding on Full HD
     *
     * @return void
     */
    public function test_widget_containers_padding()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.widget-container')
                ->assertVisible('.widget-header')
                ->assertVisible('.widget-content');
        });
    }

    /**
     * Test buttons are properly sized on Full HD
     *
     * @return void
     */
    public function test_buttons_sizing()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.btn')
                ->assertVisible('.btn-primary')
                ->assertVisible('.btn-secondary')
                ->assertVisible('.btn-danger');
        });
    }

    /**
     * Test form elements have proper spacing on Full HD
     *
     * @return void
     */
    public function test_form_elements_spacing()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/instances/create')
                ->assertVisible('input[type="text"]')
                ->assertVisible('textarea')
                ->assertVisible('select')
                ->assertVisible('.input-text');
        });
    }

    /**
     * Test table elements are properly spaced on Full HD
     *
     * @return void
     */
    public function test_table_elements_spacing()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/projects/index')
                ->assertVisible('.cyber-table')
                ->assertVisible('.cyber-table th')
                ->assertVisible('.cyber-table td');
        });
    }

    /**
     * Test calendar view on Full HD screen
     *
     * @return void
     */
    public function test_calendar_view()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/reunions/calendar')
                ->assertVisible('#calendar')
                ->assertVisible('.fc')
                ->assertVisible('.fc-daygrid-day')
                ->assertVisible('.fc-daygrid-day-number');
        });
    }

    /**
     * Test header navigation on Full HD screen
     *
     * @return void
     */
    public function test_header_navigation()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.bg-\[\#faa21b\]')
                ->assertVisible('h2:contains("Espace Élus")')
                ->assertVisible('nav')
                ->assertVisible('a:contains("Instances")')
                ->assertVisible('a:contains("Projets")')
                ->assertVisible('a:contains("Réunions")')
                ->assertVisible('a:contains("Documents")')
                ->assertVisible('a:contains("Collaboratif")')
                ->assertVisible('a:contains("Tableau de bord")');
        });
    }

    /**
     * Test responsive behavior when resizing from Full HD to smaller screens
     *
     * @return void
     */
    public function test_responsive_resizing()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('#app')
                ->resize(1280, 720)
                ->assertVisible('#app')
                ->resize(1024, 768)
                ->assertVisible('#app')
                ->resize(768, 1024)
                ->assertVisible('#app');
        });
    }

    /**
     * Test typography scaling on Full HD screen
     *
     * @return void
     */
    public function test_typography_scaling()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('h1, h2, h3, h4, h5, h6')
                ->assertVisible('p')
                ->assertVisible('span')
                ->assertVisible('a');
        });
    }

    /**
     * Test shadow effects on Full HD screen
     *
     * @return void
     */
    public function test_shadow_effects()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.shadow-lg')
                ->assertVisible('.shadow-xl')
                ->assertVisible('.glass');
        });
    }

    /**
     * Test hover effects on Full HD screen
     *
     * @return void
     */
    public function test_hover_effects()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->mouseover('.btn-primary')
                ->assertVisible('.btn-primary')
                ->mouseover('.fc-event')
                ->assertVisible('.fc-event');
        });
    }

    /**
     * Test empty state messages on Full HD screen
     *
     * @return void
     */
    public function test_empty_state_messages()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.widget-empty')
                ->assertVisible('.widget-empty-icon')
                ->assertVisible('.widget-empty-title')
                ->assertVisible('.widget-empty-description');
        });
    }

    /**
     * Test quick actions widget on Full HD screen
     *
     * @return void
     */
    public function test_quick_actions_widget()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.quick-actions-widget')
                ->assertVisible('.quick-actions-content')
                ->assertVisible('.grid-cols-2');
        });
    }

    /**
     * Test recent documents widget on Full HD screen
     *
     * @return void
     */
    public function test_recent_documents_widget()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.max-h-\[360px\]')
                ->assertVisible('.overflow-y-auto');
        });
    }

    /**
     * Test active projects table on Full HD screen
     *
     * @return void
     */
    public function test_active_projects_table()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.min-w-full')
                ->assertVisible('.divide-y')
                ->assertVisible('.overflow-x-auto');
        });
    }

    /**
     * Test navigation between pages on Full HD screen
     *
     * @return void
     */
    public function test_navigation_between_pages()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertPathIs('/elus/dashboard')
                ->clickLink('Projets')
                ->assertPathIs('/elus/projects')
                ->clickLink('Réunions')
                ->assertPathIs('/elus/reunions')
                ->clickLink('Documents')
                ->assertPathIs('/elus/documents')
                ->clickLink('Tableau de bord')
                ->assertPathIs('/elus/dashboard');
        });
    }

    /**
     * Test user menu functionality on Full HD screen
     *
     * @return void
     */
    public function test_user_menu_functionality()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('x-elus-user-menu')
                ->mouseover('x-elus-user-menu')
                ->assertVisible('a:contains("Profil")')
                ->assertVisible('form[method="POST"]');
        });
    }

    /**
     * Test admin functionality on Full HD screen
     *
     * @return void
     */
    public function test_admin_functionality()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true, 'is_admin' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('a:contains("Administration")')
                ->clickLink('Administration')
                ->assertPathIs('/elus/admin');
        });
    }

    /**
     * Test collaborative workspace on Full HD screen
     *
     * @return void
     */
    public function test_collaborative_workspace()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/collab')
                ->assertVisible('.conversation-list')
                ->assertVisible('.conversation-item')
                ->assertVisible('.message-list');
        });
    }

    /**
     * Test document library on Full HD screen
     *
     * @return void
     */
    public function test_document_library()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/documents')
                ->assertVisible('.document-grid')
                ->assertVisible('.document-card')
                ->assertVisible('.category-badge');
        });
    }

    /**
     * Test project management on Full HD screen
     *
     * @return void
     */
    public function test_project_management()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/projects')
                ->assertVisible('.project-list')
                ->assertVisible('.project-card')
                ->assertVisible('.status-badge');
        });
    }

    /**
     * Test instance management on Full HD screen
     *
     * @return void
     */
    public function test_instance_management()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/instances')
                ->assertVisible('.instance-list')
                ->assertVisible('.instance-card')
                ->assertVisible('.type-badge');
        });
    }

    /**
     * Test meeting management on Full HD screen
     *
     * @return void
     */
    public function test_meeting_management()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/reunions')
                ->assertVisible('.reunion-list')
                ->assertVisible('.reunion-card')
                ->assertVisible('.date-badge');
        });
    }

    /**
     * Test form creation on Full HD screen
     *
     * @return void
     */
    public function test_form_creation()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/instances/create')
                ->type('name', 'Test Instance')
                ->type('description', 'Test Description')
                ->select('type', 'commune')
                ->assertVisible('.btn-primary')
                ->assertVisible('.btn-secondary');
        });
    }

    /**
     * Test form editing on Full HD screen
     *
     * @return void
     */
    public function test_form_editing()
    {
        $instance = \App\Models\Instance::factory()->create();

        $this->browse(function (Browser $browser) use ($instance) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/instances/' . $instance->id . '/edit')
                ->type('name', 'Updated Instance')
                ->type('description', 'Updated Description')
                ->assertVisible('.btn-primary')
                ->assertVisible('.btn-secondary');
        });
    }

    /**
     * Test search functionality on Full HD screen
     *
     * @return void
     */
    public function test_search_functionality()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/documents')
                ->type('search', 'Test Search')
                ->keys('Enter')
                ->assertVisible('.search-results');
        });
    }

    /**
     * Test filtering functionality on Full HD screen
     *
     * @return void
     */
    public function test_filtering_functionality()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/documents')
                ->select('category', 'statutaire')
                ->assertVisible('.filtered-results');
        });
    }

    /**
     * Test pagination on Full HD screen
     *
     * @return void
     */
    public function test_pagination()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/documents')
                ->assertVisible('.pagination')
                ->assertVisible('.page-link');
        });
    }

    /**
     * Test modal dialogs on Full HD screen
     *
     * @return void
     */
    public function test_modal_dialogs()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/reunions/calendar')
                ->assertVisible('.modal')
                ->assertVisible('.modal-content');
        });
    }

    /**
     * Test notifications on Full HD screen
     *
     * @return void
     */
    public function test_notifications()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.notification-badge');
        });
    }

    /**
     * Test accessibility features on Full HD screen
     *
     * @return void
     */
    public function test_accessibility_features()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('[aria-label]')
                ->assertVisible('[aria-hidden="true"]');
        });
    }

    /**
     * Test browser compatibility on Full HD screen
     *
     * @return void
     */
    public function test_browser_compatibility()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('#app')
                ->assertVisible('.main-content')
                ->assertVisible('.glass');
        });
    }

    /**
     * Test cross-browser consistency on Full HD screen
     *
     * @return void
     */
    public function test_cross_browser_consistency()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('#app')
                ->assertVisible('.main-content')
                ->assertVisible('.glass');
        });
    }

    /**
     * Test operating system compatibility on Full HD screen
     *
     * @return void
     */
    public function test_os_compatibility()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('#app')
                ->assertVisible('.main-content')
                ->assertVisible('.glass');
        });
    }

    /**
     * Test performance on Full HD screen
     *
     * @return void
     */
    public function test_performance()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('#app')
                ->assertVisible('.main-content')
                ->assertVisible('.glass');
        });
    }

    /**
     * Test visual hierarchy on Full HD screen
     *
     * @return void
     */
    public function test_visual_hierarchy()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('h1, h2, h3, h4, h5, h6')
                ->assertVisible('p')
                ->assertVisible('span')
                ->assertVisible('a');
        });
    }

    /**
     * Test color contrast on Full HD screen
     *
     * @return void
     */
    public function test_color_contrast()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.text-white')
                ->assertVisible('.text-gray-900')
                ->assertVisible('.text-gray-600')
                ->assertVisible('.text-gray-500');
        });
    }

    /**
     * Test spacing and padding on Full HD screen
     *
     * @return void
     */
    public function test_spacing_and_padding()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('p-4')
                ->assertVisible('p-6')
                ->assertVisible('p-8')
                ->assertVisible('py-2')
                ->assertVisible('py-4')
                ->assertVisible('px-2')
                ->assertVisible('px-4')
                ->assertVisible('px-6');
        });
    }

    /**
     * Test aspect ratios on Full HD screen
     *
     * @return void
     */
    public function test_aspect_ratios()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.rounded-lg')
                ->assertVisible('.rounded-xl')
                ->assertVisible('.rounded-full');
        });
    }

    /**
     * Test immersive experience on Full HD screen
     *
     * @return void
     */
    public function test_immersive_experience()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('#app')
                ->assertVisible('.main-content')
                ->assertVisible('.glass')
                ->assertVisible('.btn-primary')
                ->assertVisible('.cyber-table');
        });
    }

    /**
     * Test fluid layout on Full HD screen
     *
     * @return void
     */
    public function test_fluid_layout()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.grid')
                ->assertVisible('.flex')
                ->assertVisible('.flex-row')
                ->assertVisible('.flex-col');
        });
    }

    /**
     * Test optimal readability on Full HD screen
     *
     * @return void
     */
    public function test_optimal_readability()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('h1, h2, h3, h4, h5, h6')
                ->assertVisible('p')
                ->assertVisible('span')
                ->assertVisible('a');
        });
    }

    /**
     * Test responsive images on Full HD screen
     *
     * @return void
     */
    public function test_responsive_images()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('img')
                ->assertVisible('svg');
        });
    }

    /**
     * Test consistent spacing on Full HD screen
     *
     * @return void
     */
    public function test_consistent_spacing()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('gap-2')
                ->assertVisible('gap-4')
                ->assertVisible('gap-6')
                ->assertVisible('gap-8');
        });
    }

    /**
     * Test visual hierarchy consistency on Full HD screen
     *
     * @return void
     */
    public function test_visual_hierarchy_consistency()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('h1, h2, h3, h4, h5, h6')
                ->assertVisible('p')
                ->assertVisible('span')
                ->assertVisible('a');
        });
    }

    /**
     * Test aspect ratio consistency on Full HD screen
     *
     * @return void
     */
    public function test_aspect_ratio_consistency()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.rounded-lg')
                ->assertVisible('.rounded-xl')
                ->assertVisible('.rounded-full');
        });
    }

    /**
     * Test immersive experience consistency on Full HD screen
     *
     * @return void
     */
    public function test_immersive_experience_consistency()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('#app')
                ->assertVisible('.main-content')
                ->assertVisible('.glass')
                ->assertVisible('.btn-primary')
                ->assertVisible('.cyber-table');
        });
    }

    /**
     * Test fluid layout consistency on Full HD screen
     *
     * @return void
     */
    public function test_fluid_layout_consistency()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('.grid')
                ->assertVisible('.flex')
                ->assertVisible('.flex-row')
                ->assertVisible('.flex-col');
        });
    }

    /**
     * Test optimal readability consistency on Full HD screen
     *
     * @return void
     */
    public function test_optimal_readability_consistency()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('h1, h2, h3, h4, h5, h6')
                ->assertVisible('p')
                ->assertVisible('span')
                ->assertVisible('a');
        });
    }

    /**
     * Test responsive images consistency on Full HD screen
     *
     * @return void
     */
    public function test_responsive_images_consistency()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('img')
                ->assertVisible('svg');
        });
    }

    /**
     * Test consistent spacing consistency on Full HD screen
     *
     * @return void
     */
    public function test_consistent_spacing_consistency()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs(\App\Models\User::factory()->create(['is_elu' => true]))
                ->visit('/elus/dashboard')
                ->assertVisible('gap-2')
                ->assertVisible('gap-4')
                ->assertVisible('gap-6')
                ->assertVisible('gap-8');
        });
    }
}
