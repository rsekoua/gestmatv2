<?php

namespace Database\Factories;

use App\Models\Materiel;
use App\Models\MaterielType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Materiel>
 */
class MaterielFactory extends Factory
{
    protected $model = Materiel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'materiel_type_id' => MaterielType::factory(),
            'marque' => fake()->randomElement(['Dell', 'HP', 'Lenovo', 'Asus', 'Acer']),
            'modele' => fake()->bothify('Model-###??'),
            'numero_serie' => fake()->unique()->bothify('SN-########'),
            'processor' => fake()->randomElement([
                'Intel Core i5-11400',
                'Intel Core i7-11700',
                'AMD Ryzen 5 5600X',
                'AMD Ryzen 7 5800X',
                'Intel Core i9-12900K',
            ]),
            'ram_size_gb' => fake()->randomElement([8, 16, 32, 64]),
            'storage_size_gb' => fake()->randomElement([256, 512, 1000, 2000]),
            'screen_size' => fake()->randomElement([13.3, 14.0, 15.6, 17.3]),
            'purchase_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'acquision' => fake()->randomElement(['Achat', 'Don', 'Location']),
            'statut' => fake()->randomElement(['disponible', 'attribué', 'en_panne', 'en_maintenance']),
            'etat_physique' => fake()->randomElement(['excellent', 'bon', 'moyen']),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * State for computer laptops.
     */
    public function laptop(): static
    {
        return $this->state(fn (array $attributes) => [
            'materiel_type_id' => MaterielType::where('nom', 'Ordinateur Portable')->first()?->id
                ?? MaterielType::factory()->state(['nom' => 'Ordinateur Portable']),
            'screen_size' => fake()->randomElement([13.3, 14.0, 15.6]),
        ]);
    }

    /**
     * State for desktop computers.
     */
    public function desktop(): static
    {
        return $this->state(fn (array $attributes) => [
            'materiel_type_id' => MaterielType::where('nom', 'Ordinateur Bureau')->first()?->id
                ?? MaterielType::factory()->state(['nom' => 'Ordinateur Bureau']),
            'screen_size' => null,
        ]);
    }

    /**
     * State for available materiel.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'disponible',
        ]);
    }

    /**
     * State for attributed materiel.
     */
    public function attributed(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'attribué',
        ]);
    }
}
