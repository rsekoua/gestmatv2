<?php

namespace Database\Factories;

use App\Models\MaterielType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaterielType>
 */
class MaterielTypeFactory extends Factory
{
    protected $model = MaterielType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->words(2, true),
            'description' => fake()->sentence(),
        ];
    }
}
