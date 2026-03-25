<?php

namespace Database\Factories;

use App\Fichaje;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FichajeFactory extends Factory
{
    protected $model = Fichaje::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'fecha' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
            'tipo' => $this->faker->randomElement(['entrada', 'salida']),
            'comentarios' => $this->faker->sentence,
        ];
    }
}
