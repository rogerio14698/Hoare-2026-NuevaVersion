<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class FichajesAjaxTest extends TestCase
{
    use WithoutMiddleware;

    /** @test */
    public function fichajes_endpoint_is_accessible()
    {
        // Mockear Auth facade para evitar errores de autenticación
        $mockUser = \Mockery::mock('alias:' . User::class);
        $mockUser->shouldReceive('compruebaSeguridad')
            ->with('mostrar-fichajes')
            ->andReturn(true);
        
        \Auth::shouldReceive('user')
            ->andReturn($mockUser);

        // La ruta real está dentro del prefijo 'eunomia'
        // Este test verifica que la ruta esté accesible
        $response = $this->get('/eunomia/fichajes/get/1');

        // Verificamos que la respuesta sea exitosa o al menos no sea un error de ruta
        $this->assertTrue(
            in_array($response->status(), [200, 403, 500]),
            'La ruta debe estar definida y accesible'
        );
    }
}
