<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Field;
use App\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Field>
 */
class FieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fieldTypes = ['text', 'email', 'number', 'textarea', 'select', 'checkbox', 'radio'];
        $type = $this->faker->randomElement($fieldTypes);

        $configuration = [
            'type' => $type,
            'name' => $this->faker->slug(),
            'label' => [
                'en' => $this->faker->words(2, true),
                'de' => $this->faker->words(2, true),
            ],
            'required' => $this->faker->boolean(),
            'class' => 'form-control',
            'placeholder' => [
                'en' => $this->faker->sentence(),
                'de' => $this->faker->sentence(),
            ],
        ];

        // Add type-specific configuration
        if (in_array($type, ['select', 'radio'])) {
            $configuration['options'] = [
                [
                    'value' => 'option1',
                    'label' => [
                        'en' => 'Option 1',
                        'de' => 'Option 1',
                    ],
                ],
                [
                    'value' => 'option2',
                    'label' => [
                        'en' => 'Option 2',
                        'de' => 'Option 2',
                    ],
                ],
            ];
        }

        if ($type === 'textarea') {
            $configuration['rows'] = $this->faker->numberBetween(3, 6);
        }

        if (in_array($type, ['number', 'range'])) {
            $configuration['min'] = 0;
            $configuration['max'] = 100;
            $configuration['step'] = 1;
        }

        return [
            'form_id' => Form::factory(),
            'type' => $type,
            'order' => $this->faker->numberBetween(1, 10),
            'configuration' => $configuration,
            'validation_rules' => null,
        ];
    }

    /**
     * Create a text field specifically.
     */
    public function text(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'text',
                'configuration' => [
                    'type' => 'text',
                    'name' => $this->faker->slug(),
                    'label' => [
                        'en' => $this->faker->words(2, true),
                        'de' => $this->faker->words(2, true),
                    ],
                    'required' => $this->faker->boolean(),
                    'class' => 'form-control',
                    'placeholder' => [
                        'en' => $this->faker->sentence(),
                        'de' => $this->faker->sentence(),
                    ],
                ],
            ];
        });
    }
}
