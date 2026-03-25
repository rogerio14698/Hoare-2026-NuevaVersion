<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Tests para verificar la configuración básica de la aplicación
 */
class ApplicationConfigTest extends TestCase
{
    /** @test */
    public function app_environment_is_testing()
    {
        $this->assertEquals('testing', app()->environment(),
            'El entorno de la aplicación debe ser "testing" durante los tests');
    }

    /** @test */
    public function app_has_correct_locale()
    {
        // Verificar que la aplicación tiene un locale configurado
        $this->assertNotEmpty(config('app.locale'),
            'La aplicación debe tener un locale configurado');
    }

    /** @test */
    public function cache_driver_is_array_for_testing()
    {
        $this->assertEquals('array', config('cache.default'),
            'El driver de cache debe ser "array" durante tests');
    }

    /** @test */
    public function session_driver_is_array_for_testing()
    {
        $this->assertEquals('array', config('session.driver'),
            'El driver de sesión debe ser "array" durante tests');
    }

    /** @test */
    public function database_connection_is_sqlite_for_testing()
    {
        $this->assertEquals('sqlite', config('database.default'),
            'La conexión de base de datos debe ser "sqlite" durante tests');
    }

    /** @test */
    public function app_key_is_set()
    {
        $this->assertNotEmpty(config('app.key'),
            'La clave de la aplicación debe estar configurada');
    }

    /** @test */
    public function app_timezone_is_configured()
    {
        $this->assertNotEmpty(config('app.timezone'),
            'El timezone de la aplicación debe estar configurado');
    }
}
