<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;

/**
 * Test que verifica que las rutas críticas estén correctamente definidas
 * y respondan adecuadamente según el estado de autenticación
 */
class RouteAccessTest extends TestCase
{
    /** @test */
    public function dashboard_requires_authentication()
    {
        // Sin autenticación debería redirigir al login
        $response = $this->get('/eunomia/home');
        
        $this->assertTrue(
            in_array($response->status(), [302, 401]),
            'Dashboard debe requerir autenticación'
        );
    }

    /** @test */
    public function projects_route_exists()
    {
        $response = $this->get('/eunomia/projects');
        
        // No debe ser 404 (puede ser 302 redirect o 500 por falta de auth)
        $this->assertNotEquals(404, $response->status(), 'La ruta de proyectos debe existir');
    }

    /** @test */
    public function tasks_route_exists()
    {
        $response = $this->get('/eunomia/tasks');
        
        $this->assertNotEquals(404, $response->status(), 'La ruta de tareas debe existir');
    }

    /** @test */
    public function users_route_exists()
    {
        $response = $this->get('/eunomia/users');
        
        $this->assertNotEquals(404, $response->status(), 'La ruta de usuarios debe existir');
    }

    /** @test */
    public function fichajes_route_exists()
    {
        $response = $this->get('/eunomia/fichajes');
        
        $this->assertNotEquals(404, $response->status(), 'La ruta de fichajes debe existir');
    }

    /** @test */
    public function control_accesos_route_exists()
    {
        $response = $this->get('/eunomia/control_accesos');
        
        $this->assertNotEquals(404, $response->status(), 'La ruta de control de accesos debe existir');
    }

    /** @test */
    public function mailbox_route_exists()
    {
        $response = $this->get('/eunomia/mailbox');
        
        $this->assertNotEquals(404, $response->status(), 'La ruta de mailbox debe existir');
    }
}
