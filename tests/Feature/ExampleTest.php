<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
    $response = $this->get('/');

    // The application redirects the root path to /login, expect a 302 redirect.
    $response->assertRedirect('/login');
    }
}
