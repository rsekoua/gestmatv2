<?php

namespace Database\Factories;

use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attribution>
 */
class AttributionFactory extends Factory
{
    protected $model = Attribution::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateAttribution = fake()->dateTimeBetween('-2 years', 'now');

        return [
            'materiel_id' => Materiel::factory(),
            'employee_id' => Employee::factory(),
            'date_attribution' => $dateAttribution,
            'date_restitution' => null,
            'observations_att' => fake()->optional()->sentence(),
        ];
    }

    /**
     * State for active attribution (not returned).
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_restitution' => null,
            'observations_res' => null,
            'etat_general_res' => null,
            'etat_fonctionnel_res' => null,
            'decision_res' => null,
            'dommages_res' => null,
        ]);
    }

    /**
     * State for closed attribution (returned).
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_restitution' => fake()->dateTimeBetween($attributes['date_attribution'], 'now'),
            'observations_res' => fake()->sentence(),
            'etat_general_res' => fake()->randomElement(['excellent', 'bon', 'moyen', 'mauvais']),
            'etat_fonctionnel_res' => fake()->randomElement(['parfait', 'defauts_mineurs', 'dysfonctionnements', 'hors_service']),
            'decision_res' => fake()->randomElement(['remis_en_stock', 'a_reparer', 'rebut']),
            'dommages_res' => fake()->optional()->sentence(),
        ]);
    }
}
