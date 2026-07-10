<?php

namespace Tests\Feature\Explore;

use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * Contract tests for the Inertia/JSON Explore surface. The full migration set
 * cannot run on the sqlite test DB (a demography backfill references a missing
 * `countries` table), so — following the project's existing pattern — we build
 * only the `circles` table and exercise the empty-data / no-DB paths here.
 * Full-data behaviour is covered by the real-DB runtime smoke.
 */
class ExploreEndpointsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('circles')) {
            (include database_path('migrations/2026_06_19_000001_create_circles_table.php'))->up();
            (include database_path('migrations/2026_06_23_000003_add_locatable_to_circles_table.php'))->up();
            (include database_path('migrations/2026_07_01_000011_add_deleted_at_to_circles_table.php'))->up();
            (include database_path('migrations/2026_07_07_000002_add_status_to_circles_table.php'))->up();
        }
    }

    public function test_explore_index_renders_inertia_page_with_expected_prop_shape(): void
    {
        $this->get('/explore')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Explore/Index')
                // URL-state echo — bottom section defaults to Theme Communities.
                ->where('filters.type', null)
                ->where('filters.community', 'ThemeCommunity')
                ->where('filters.circle', null)
                ->where('filters.view', 'browse')
                // Geographic anchor is always South Africa (national).
                ->where('breadcrumb.0.name', 'South Africa')
                ->where('currentLevel', 'National')
                ->where('isAtTerminalLevel', false)
                // Empty DB → empty lists, but the structure is intact.
                ->has('location.communities', 0)
                ->where('location.countBelow', 0)
                ->has('typeSection.communities', 0)
                ->where('typeSection.addLabel', 'a Theme Community')
                ->has('meta.top')
                ->has('meta.bottom')
                ->has('filterBars.location', 1)
                ->has('filterBars.community', 5)
                ->where('filterBars.community.0.value', 'ThemeCommunity'),
            );
    }

    public function test_explore_index_accepts_type_and_community_url_state(): void
    {
        $this->get('/explore?type=Organisation&community=Campaign')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('filters.type', 'Organisation')
                ->where('filters.community', 'Campaign')
                ->where('typeSection.addLabel', 'a Campaign Community'),
            );
    }

    public function test_search_requires_at_least_two_characters(): void
    {
        // Under 2 chars short-circuits before any DB query.
        $this->getJson('/explore/search?q=a')
            ->assertOk()
            ->assertExactJson(['results' => []]);
    }

    public function test_search_returns_results_shape(): void
    {
        // Empty circles table → no matches, but the JSON contract holds.
        $this->getJson('/explore/search?q=zzz-none')
            ->assertOk()
            ->assertJsonStructure(['results']);
    }

    public function test_nearest_requires_valid_coordinates(): void
    {
        $this->getJson('/explore/nearest')->assertStatus(422);
        $this->getJson('/explore/nearest?lat=999&lng=0')->assertStatus(422);
    }
}
