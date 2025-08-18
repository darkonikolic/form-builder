<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Form;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Form>
 */
class FormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => [
                'en' => $this->faker->sentence(3),
                'de' => $this->faker->sentence(3),
            ],
            'description' => [
                'en' => $this->faker->paragraph(),
                'de' => $this->faker->paragraph(),
            ],
            'is_active' => true,
            'configuration' => [
                'locales' => ['en', 'de'],
            ],
        ];
    }
}
