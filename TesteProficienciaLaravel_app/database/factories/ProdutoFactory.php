<?php

namespace Database\Factories;

use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produto>
 */
class ProdutoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = \App\Models\Produto::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->word(),
            'descricao' => $this->faker->text('100'),
            'preco' => $this->faker->randomFloat(2, 1, 2000),
        ];
    }
}
