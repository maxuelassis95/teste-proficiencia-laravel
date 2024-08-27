<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = \App\Models\Cliente::class; 

    public function definition(): array
    {
        return [
            'nome' => $this->faker->word(),
            'email' => $this->faker->unique()->safeEmail,
            'endereco' => $this->faker->address(),
            'telefone' => $this->faker->phoneNumber(),
        ];
    }
}
