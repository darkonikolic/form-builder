<?php

declare(strict_types=1);

use App\Models\Form;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Create user and authenticate with Sanctum
    $this->user = $this->createAuthenticatedUser();
});

test('user can list forms', function (): void {
    // Create some forms for the user
    Form::factory()->create([
        'user_id' => $this->user->id,
    ]);

    Form::factory()->create([
        'user_id' => $this->user->id,
    ]);

    // Create forms for another user (should not appear)
    $otherUser = $this->createUser();
    Form::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get('/api/forms');

    $this->assertSuccessfulApiResponse($response, 'Forms retrieved successfully');

    // Should only return 2 forms for the authenticated user
    expect($response->json('data'))->toHaveCount(2);
});

test('user can create form', function (): void {
    $formData = $this->getFormData();

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', $formData);

    $this->assertCreatedApiResponse($response, 'Form created successfully');

    // Verify form was created in database
    $this->assertDatabaseHas('forms', [
        'user_id' => $this->user->id,
        'is_active' => true,
    ]);
});

test('user cannot create form with invalid data', function (): void {
    $invalidData = [
        'name' => [
            'en' => 'Contact Form',
            // Missing 'de' locale
        ],
        'configuration' => [
            'locales' => ['en', 'de'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', $invalidData);

    $this->assertValidationError($response, ['name.de']);
});

test('user cannot create form with invalid locales', function (): void {
    $invalidData = [
        'name' => [
            'en' => 'Contact Form',
            'de' => 'Kontaktformular',
        ],
        'configuration' => [
            'locales' => ['en', 'invalid_locale'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', $invalidData);

    $this->assertValidationError($response, ['configuration.locales.1']);
});

test('user can show form', function (): void {
    $form = $this->createFormForUser($this->user);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get("/api/forms/{$form->id}");

    $this->assertSuccessfulApiResponse($response, 'Form retrieved successfully');

    $response->assertJson([
        'data' => [
            'id' => $form->id,
            'user_id' => $this->user->id,
        ],
    ]);
});

test('user cannot show nonexistent form', function (): void {
    $fakeId = '550e8400-e29b-41d4-a716-446655440000';

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get("/api/forms/{$fakeId}");

    $this->assertNotFoundError($response, 'Form not found');
});

test('user cannot show another users form', function (): void {
    $otherUser = $this->createUser();
    $form = $this->createFormForUser($otherUser);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get("/api/forms/{$form->id}");

    $this->assertNotFoundError($response, 'Form not found');
});

test('user gets 401 without authentication', function (): void {
    Auth::forgetGuards();

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get('/api/forms');

    $this->assertUnauthorizedError($response);
});

test('user gets 401 with invalid token', function (): void {
    // Clear any existing authentication
    Auth::forgetGuards();

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer invalid_token',
    ])->get('/api/forms');

    $this->assertUnauthorizedError($response);
});

test('user gets 401 when trying to access protected endpoint without auth', function (): void {
    Auth::forgetGuards();

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', []);

    $this->assertUnauthorizedError($response);
});

test('user can update form', function (): void {
    $form = $this->createFormForUser($this->user);

    $updateData = [
        'name' => [
            'en' => 'Updated Contact Form',
            'de' => 'Aktualisiertes Kontaktformular',
        ],
        'description' => [
            'en' => 'Updated contact form',
            'de' => 'Aktualisiertes Kontaktformular',
        ],
        'is_active' => false,
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$form->id}", $updateData);

    $this->assertSuccessfulApiResponse($response, 'Form updated successfully');

    // Verify form was updated in database
    $this->assertDatabaseHas('forms', [
        'id' => $form->id,
        'user_id' => $this->user->id,
        'is_active' => false,
    ]);
});

test('user cannot update nonexistent form', function (): void {
    $fakeId = '550e8400-e29b-41d4-a716-446655440000';

    $updateData = [
        'name' => [
            'en' => 'Updated Name',
            'de' => 'Aktualisierter Name',
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$fakeId}", $updateData);

    $this->assertNotFoundError($response, 'Form not found');
});

test('user cannot update another users form', function (): void {
    $otherUser = $this->createUser();
    $form = $this->createFormForUser($otherUser);

    $updateData = [
        'name' => [
            'en' => 'Updated Name',
            'de' => 'Aktualisierter Name',
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$form->id}", $updateData);

    $this->assertNotFoundError($response, 'Form not found');
});

test('user cannot update form with invalid data', function (): void {
    $form = $this->createFormForUser($this->user);

    $invalidData = [
        'name' => [
            'en' => 'Updated Contact Form',
            // Missing 'de' locale
        ],
        'configuration' => [
            'locales' => ['en', 'de'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$form->id}", $invalidData);

    $this->assertValidationError($response, ['name.de']);
});

test('user can delete form', function (): void {
    $form = $this->createFormForUser($this->user);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->delete("/api/forms/{$form->id}");

    $this->assertSuccessfulApiResponse($response, 'Form deleted successfully', false);

    // Verify form was deleted from database
    $this->assertDatabaseMissing('forms', [
        'id' => $form->id,
    ]);
});

test('user cannot delete nonexistent form', function (): void {
    $fakeId = '550e8400-e29b-41d4-a716-446655440000';

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->delete("/api/forms/{$fakeId}");

    $this->assertNotFoundError($response, 'Form not found');
});

test('user cannot delete another users form', function (): void {
    $otherUser = $this->createUser();
    $form = $this->createFormForUser($otherUser);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->delete("/api/forms/{$form->id}");

    $this->assertNotFoundError($response, 'Form not found');
});

test('form deletion cascades to fields', function (): void {
    $form = $this->createFormForUser($this->user);

    // Create a field for the form
    $field = \App\Models\Field::create([
        'form_id' => $form->id,
        'type' => 'text',
        'order' => 1,
        'configuration' => [
            'type' => 'text',
            'name' => 'test_field',
            'label' => ['en' => 'Test Field', 'de' => 'Test Feld'],
        ],
    ]);

    // Delete the form
    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->delete("/api/forms/{$form->id}");

    $this->assertSuccessfulApiResponse($response, 'Form deleted successfully', false);

    // Verify both form and field were deleted
    $this->assertDatabaseMissing('forms', ['id' => $form->id]);
    $this->assertDatabaseMissing('fields', ['id' => $field->id]);
});

test('form validation requires name for all locales', function (): void {
    $formData = [
        'name' => [
            'en' => 'Contact Form',
            // Missing 'de' locale
        ],
        'configuration' => [
            'locales' => ['en', 'de'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', $formData);

    $this->assertValidationError($response, ['name.de']);
});

test('form validation requires configuration locales', function (): void {
    $formData = [
        'name' => [
            'en' => 'Contact Form',
            'de' => 'Kontaktformular',
        ],
        // Missing configuration
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', $formData);

    $this->assertValidationError($response, ['configuration']);
});

test('form validation accepts valid locales', function (): void {
    $formData = [
        'name' => [
            'en' => 'Contact Form',
            'de' => 'Kontaktformular',
        ],
        'description' => [
            'en' => 'Contact form',
            'de' => 'Kontaktformular',
        ],
        'is_active' => true,
        'configuration' => [
            'locales' => ['en', 'de'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', $formData);

    $this->assertCreatedApiResponse($response, 'Form created successfully');
});

test('form validation rejects invalid locales', function (): void {
    $formData = [
        'name' => [
            'en' => 'Contact Form',
            'invalid_locale' => 'Invalid Form',
        ],
        'configuration' => [
            'locales' => ['en', 'invalid_locale'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', $formData);

    $this->assertValidationError($response, ['configuration.locales.1']);
});

test('form validation accepts single locale', function (): void {
    $formData = [
        'name' => [
            'en' => 'Contact Form',
        ],
        'description' => [
            'en' => 'Contact form',
        ],
        'configuration' => [
            'locales' => ['en'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', $formData);

    $this->assertCreatedApiResponse($response, 'Form created successfully');
});

test('form validation rejects zero locales', function (): void {
    $formData = [
        'name' => [
            'en' => 'Contact Form',
        ],
        'description' => [
            'en' => 'Contact form',
        ],
        'configuration' => [
            'locales' => [],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', $formData);

    $this->assertValidationError($response, ['configuration.locales']);
});
