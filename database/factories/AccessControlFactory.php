<?php

namespace Database\Factories;

use App\AccessControl;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccessControlFactory extends Factory
{
    protected $model = AccessControl::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'ip' => $this->faker->ipv4,
            'zip_code' => $this->faker->postcode,
            'location' => $this->faker->city,
            'created_at' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
            'updated_at' => now(),
        ];
    }
}
