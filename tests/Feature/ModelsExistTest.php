<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Tests para verificar que los modelos críticos están correctamente definidos
 */
class ModelsExistTest extends TestCase
{
    /** @test */
    public function user_model_exists()
    {
        $this->assertTrue(
            class_exists(\App\User::class),
            'El modelo User debe existir'
        );
    }

    /** @test */
    public function fichaje_model_exists()
    {
        $this->assertTrue(
            class_exists(\App\Fichaje::class),
            'El modelo Fichaje debe existir'
        );
    }

    /** @test */
    public function access_control_model_exists()
    {
        $this->assertTrue(
            class_exists(\App\AccessControl::class),
            'El modelo AccessControl debe existir'
        );
    }

    /** @test */
    public function project_model_exists()
    {
        $this->assertTrue(
            class_exists(\App\Project::class),
            'El modelo Project debe existir'
        );
    }

    /** @test */
    public function task_model_exists()
    {
        $this->assertTrue(
            class_exists(\App\Task::class),
            'El modelo Task debe existir'
        );
    }

    /** @test */
    public function customer_model_exists()
    {
        $this->assertTrue(
            class_exists(\App\Customer::class),
            'El modelo Customer debe existir'
        );
    }

    /** @test */
    public function critical_controllers_exist()
    {
        $controllers = [
            \App\Http\Controllers\FichajeController::class,
            \App\Http\Controllers\AccessControlController::class,
            \App\Http\Controllers\HoraeProjectController::class,
            \App\Http\Controllers\HoraeTaskController::class,
            \App\Http\Controllers\HoraeUserController::class,
        ];

        foreach ($controllers as $controller) {
            $this->assertTrue(
                class_exists($controller),
                "El controlador {$controller} debe existir"
            );
        }
    }
}
