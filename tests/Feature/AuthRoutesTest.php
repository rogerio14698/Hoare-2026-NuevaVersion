<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    /** @test */
    public function root_redirects_to_login()
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function protected_eunomia_redirects_to_login_if_not_authenticated()
    {
        $response = $this->get('/eunomia');
        $response->assertRedirect('/login');
    }
}
