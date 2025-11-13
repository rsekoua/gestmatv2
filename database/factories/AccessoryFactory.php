<?php

namespace Database\Factories;

use App\Models\Accessory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accessory>
 */
class AccessoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Accessory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $accessories = [
            ['nom' => 'Souris sans fil', 'description' => 'Souris optique sans fil'],
            ['nom' => 'Clavier', 'description' => 'Clavier AZERTY filaire'],
            ['nom' => 'Chargeur', 'description' => 'Chargeur d\'ordinateur portable'],
            ['nom' => 'Sacoche', 'description' => 'Sacoche de transport pour ordinateur'],
            ['nom' => 'Câble HDMI', 'description' => 'Câble HDMI 2m'],
            ['nom' => 'Hub USB', 'description' => 'Hub USB 4 ports'],
            ['nom' => 'Webcam', 'description' => 'Webcam HD 1080p'],
            ['nom' => 'Casque audio', 'description' => 'Casque audio avec microphone'],
        ];

        $accessory = fake()->randomElement($accessories);

        return [
            'nom' => $accessory['nom'],
            'description' => $accessory['description'],
        ];
    }
}
