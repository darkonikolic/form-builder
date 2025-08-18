<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Form;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected Form $form;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser();
    }

    /**
     * Assert created API response structure (with optional data field).
     */
    protected function assertCreatedApiResponse($response, string $message = null, bool $hasData = true): void
    {
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                ...($hasData ? ['data'] : []),
            ])
            ->assertJson([
                'success' => true,
            ]);

        if ($message) {
            $response->assertJson([
                'message' => $message,
            ]);
        }
    }

    /**
     * Assert not found error response.
     */
    protected function assertNotFoundError($response, string $message = 'Resource not found'): void
    {
        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => $message,
            ]);
    }

    /**
     * Assert successful API response structure (with optional data field).
     */
    protected function assertSuccessfulApiResponse($response, string $message = null, bool $hasData = true): void
    {
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                ...($hasData ? ['data'] : []),
            ])
            ->assertJson([
                'success' => true,
            ]);

        if ($message) {
            $response->assertJson([
                'message' => $message,
            ]);
        }
    }

    /**
     * Assert unauthorized error response.
     */
    protected function assertUnauthorizedError($response, string $message = 'Unauthenticated'): void
    {
        $response->assertStatus(401)
            ->assertJson([
                'message' => $message . '.',
            ]);
    }

    /**
     * Assert validation error response.
     */
    protected function assertValidationError($response, array $errors, string $message = 'Validation failed'): void
    {
        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ])
            ->assertJson([
                'success' => false,
                'message' => $message,
            ]);

        foreach ($errors as $field) {
            $response->assertJsonValidationErrors([$field]);
        }
    }

    /**
     * Create and authenticate a user.
     */
    protected function createAuthenticatedUser(): User
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        return $user;
    }

    /**
     * Create a form for the authenticated user.
     */
    protected function createFormForUser(User $user, array $attributes = []): Form
    {
        $defaultAttributes = [
            'user_id' => $user->id,
            'name' => ['en' => 'Test Form', 'de' => 'Test Formular'],
            'description' => ['en' => 'Test Description', 'de' => 'Test Beschreibung'],
            'is_active' => true,
            'configuration' => ['locales' => ['en', 'de']],
        ];

        return Form::create(array_merge($defaultAttributes, $attributes));
    }

    /**
     * Create a form with specific locales.
     */
    protected function createFormWithLocales(User $user, array $locales = ['en', 'de']): Form
    {
        return $this->createFormForUser($user, [
            'name' => array_fill_keys($locales, 'Test Form'),
            'description' => array_fill_keys($locales, 'Test Description'),
            'configuration' => ['locales' => $locales],
        ]);
    }

    /**
     * Create a basic user.
     */
    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    /**
     * Get common field data for testing.
     */
    protected function getFieldData(string $type = 'text', array $overrides = []): array
    {
        $defaultData = [
            'type' => $type,
            'configuration' => [
                'type' => $type,
                'name' => 'test_field',
                'label' => [
                    'en' => 'Test Field',
                    'de' => 'Test Feld',
                ],
                'required' => true,
            ],
        ];

        // Add special configuration for select and radio fields
        if (in_array($type, ['select', 'radio'])) {
            $defaultData['configuration']['options'] = [
                [
                    'value' => 'option1',
                    'label' => ['en' => 'Option 1', 'de' => 'Option 1'],
                ],
                [
                    'value' => 'option2',
                    'label' => ['en' => 'Option 2', 'de' => 'Option 2'],
                ],
            ];
        }

        return array_merge($defaultData, $overrides);
    }

    /**
     * Get common form data for testing.
     */
    protected function getFormData(array $overrides = []): array
    {
        $defaultData = [
            'name' => [
                'en' => 'Contact Form',
                'de' => 'Kontaktformular',
            ],
            'description' => [
                'en' => 'A contact form for customer inquiries',
                'de' => 'Ein Kontaktformular für Kundenanfragen',
            ],
            'is_active' => true,
            'configuration' => [
                'locales' => ['en', 'de'],
            ],
        ];

        return array_merge($defaultData, $overrides);
    }
}
