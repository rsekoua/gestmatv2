<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'emploi' => fake()->randomElement([
                'Technicien',
                'Ingénieur',
                'Chef de projet',
                'Développeur',
                'Administrateur système',
                'Responsable',
                'Assistant',
                'Analyste',
            ]),
            'email' => fake()->unique()->safeEmail(),
            'telephone' => fake()->numerify('06########'),
            'fonction' => fake()->randomElement([
                'Cadre',
                'Agent de maîtrise',
                'Employé',
                'Technicien',
            ]),
        ];
    }
}
