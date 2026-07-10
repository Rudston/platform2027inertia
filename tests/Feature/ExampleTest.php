<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * The home route redirects to the Explore feature (the focus of this build).
     */
    public function test_the_home_route_redirects_to_explore(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('explore'));
    }
}
