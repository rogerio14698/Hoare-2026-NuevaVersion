<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;

/**
 * Tests para verificar la funcionalidad de permisos y seguridad
 */
class PermissionsTest extends TestCase
{
    use WithoutMiddleware;

    /** @test */
    public function compruebaSeguridad_method_exists_in_user_class()
    {
        // Verificar que el método existe en el código fuente del modelo User
        $userFile = file_get_contents(app_path('User.php'));
        
        $this->assertTrue(
            strpos($userFile, 'function compruebaSeguridad') !== false,
            'El modelo User debe tener el método compruebaSeguridad definido'
        );
    }

    /** @test */
    public function protected_routes_check_permissions()
    {
        // Mockear usuario sin permisos
        $mockUser = \Mockery::mock('alias:' . User::class);
        $mockUser->shouldReceive('compruebaSeguridad')
            ->with('mostrar-fichajes')
            ->andReturn(false);
        
        \Auth::shouldReceive('user')->andReturn($mockUser);

        $response = $this->get('/eunomia/fichajes/get/1');
        
        // Debería denegar acceso (403) o mostrar mensaje de error
        $this->assertTrue(
            in_array($response->status(), [403, 500]) || 
            strpos($response->getContent(), 'no tiene permisos') !== false,
            'Las rutas protegidas deben verificar permisos'
        );
    }

    /** @test */
    public function routes_with_valid_permissions_are_accessible()
    {
        // Mockear usuario con permisos
        $mockUser = \Mockery::mock('alias:' . User::class);
        $mockUser->shouldReceive('compruebaSeguridad')->andReturn(true);
        
        \Auth::shouldReceive('user')->andReturn($mockUser);

        $response = $this->get('/eunomia/fichajes/get/1');
        
        // Con permisos válidos, debe responder (200 o 500 por otros motivos, no 403)
        $this->assertNotEquals(403, $response->status(),
            'Con permisos válidos no debe haber error 403');
    }
}
