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
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
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
    $otherUser = User::factory()->create();
    Form::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get('/api/forms');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'name',
                    'description',
                    'is_active',
                    'configuration',
                    'created_at',
                    'updated_at',
                ],
            ],
            'message',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Forms retrieved successfully',
        ]);

    // Should only return 2 forms for the authenticated user
    expect($response->json('data'))->toHaveCount(2);
});

test('user can create form', function (): void {
    $formData = [
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

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', $formData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'user_id',
                'name',
                'description',
                'is_active',
                'configuration',
                'created_at',
                'updated_at',
            ],
            'message',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Form created successfully',
            'data' => [
                'name' => $formData['name'],
                'description' => $formData['description'],
                'is_active' => $formData['is_active'],
                'configuration' => $formData['configuration'],
            ],
        ]);

    // Verify form was saved to database
    $this->assertDatabaseHas('forms', [
        'user_id' => $this->user->id,
        'is_active' => true,
    ]);

    $form = Form::where('user_id', $this->user->id)->first();
    expect($form->name)->toBe($formData['name']);
    expect($form->description)->toBe($formData['description']);
    expect($form->configuration)->toBe($formData['configuration']);
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

    $response->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
            'errors' => [
                'name.de',
            ],
        ])
        ->assertJson([
            'success' => false,
            'message' => 'Validation failed',
        ]);
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

    $response->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
            'errors' => [
                'configuration.locales.1',
            ],
        ]);
});

test('user can show form', function (): void {
    $form = Form::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get("/api/forms/{$form->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'user_id',
                'name',
                'description',
                'is_active',
                'configuration',
                'created_at',
                'updated_at',
                'fields',
            ],
            'message',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Form retrieved successfully',
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

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Form not found',
        ]);
});

test('user cannot show another users form', function (): void {
    $otherUser = User::factory()->create();
    $form = Form::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get("/api/forms/{$form->id}");

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Form not found',
        ]);
});

test('user gets 401 without authentication', function (): void {
    // Test without any authentication - should get 401
    Auth::forgetGuards();

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get('/api/forms');

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
});

test('user gets 401 with invalid token', function (): void {
    // Test with invalid token - should get 401
    Auth::forgetGuards();

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer invalid-token',
    ])->get('/api/forms');

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
});

test('user gets 401 when trying to access protected endpoint without auth', function (): void {
    // Test without authentication - should get 401
    Auth::forgetGuards();

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', [
        'name' => ['en' => 'Test Form', 'de' => 'Test Formular'],
        'description' => ['en' => 'Test Description', 'de' => 'Test Beschreibung'],
        'is_active' => true,
        'configuration' => ['locales' => ['en', 'de']],
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
});

test('user can update form', function (): void {
    $form = Form::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $updateData = [
        'name' => [
            'en' => 'Updated Contact Form',
            'de' => 'Aktualisiertes Kontaktformular',
        ],
        'is_active' => false,
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$form->id}", $updateData);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'user_id',
                'name',
                'description',
                'is_active',
                'configuration',
                'created_at',
                'updated_at',
            ],
            'message',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Form updated successfully',
            'data' => [
                'name' => $updateData['name'],
                'is_active' => $updateData['is_active'],
            ],
        ]);

    // Verify form was updated in database
    $this->assertDatabaseHas('forms', [
        'id' => $form->id,
        'is_active' => false,
    ]);

    $updatedForm = Form::find($form->id);
    expect($updatedForm->name)->toBe($updateData['name']);
    expect($updatedForm->is_active)->toBe($updateData['is_active']);
});

test('user cannot update nonexistent form', function (): void {
    $fakeId = '550e8400-e29b-41d4-a716-446655440000';
    $updateData = [
        'name' => [
            'en' => 'Updated Contact Form',
            'de' => 'Aktualisiertes Kontaktformular',
        ],
        'is_active' => false,
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$fakeId}", $updateData);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Form not found',
        ]);
});

test('user cannot update another users form', function (): void {
    $otherUser = User::factory()->create();
    $form = Form::create([
        'user_id' => $otherUser->id,
        'name' => ['en' => 'Other Form', 'de' => 'Anderes Formular'],
        'description' => ['en' => 'Other Description', 'de' => 'Andere Beschreibung'],
        'is_active' => true,
        'configuration' => ['locales' => ['en', 'de']],
    ]);

    $updateData = [
        'name' => [
            'en' => 'Updated Contact Form',
            'de' => 'Aktualisiertes Kontaktformular',
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$form->id}", $updateData);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Form not found',
        ]);
});

test('user cannot update form with invalid data', function (): void {
    $form = Form::create([
        'user_id' => $this->user->id,
        'name' => ['en' => 'Test Form', 'de' => 'Test Formular'],
        'description' => ['en' => 'Test Description', 'de' => 'Test Beschreibung'],
        'is_active' => true,
        'configuration' => ['locales' => ['en', 'de']],
    ]);

    $invalidData = [
        'name' => [
            'en' => 'Updated Contact Form',
            // Missing 'de' locale
        ],
        'configuration' => [
            'locales' => ['en'], // Update locales to match the new name structure
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$form->id}", $invalidData);

    $response->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
            'errors' => [
                'name.de',
            ],
        ])
        ->assertJson([
            'success' => false,
            'message' => 'Validation failed',
        ]);
});

test('user can delete form', function (): void {
    $form = Form::create([
        'user_id' => $this->user->id,
        'name' => ['en' => 'Test Form', 'de' => 'Test Formular'],
        'description' => ['en' => 'Test Description', 'de' => 'Test Beschreibung'],
        'is_active' => true,
        'configuration' => ['locales' => ['en', 'de']],
    ]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->delete("/api/forms/{$form->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Form deleted successfully',
        ]);

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

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Form not found',
        ]);
});

test('user cannot delete another users form', function (): void {
    $otherUser = User::factory()->create();
    $form = Form::create([
        'user_id' => $otherUser->id,
        'name' => ['en' => 'Other Form', 'de' => 'Anderes Formular'],
        'description' => ['en' => 'Other Description', 'de' => 'Andere Beschreibung'],
        'is_active' => true,
        'configuration' => ['locales' => ['en', 'de']],
    ]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->delete("/api/forms/{$form->id}");

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Form not found',
        ]);
});

test('form deletion cascades to fields', function (): void {
    $form = Form::create([
        'user_id' => $this->user->id,
        'name' => ['en' => 'Test Form', 'de' => 'Test Formular'],
        'description' => ['en' => 'Test Description', 'de' => 'Test Beschreibung'],
        'is_active' => true,
        'configuration' => ['locales' => ['en', 'de']],
    ]);

    // Create some fields for the form
    $form->fields()->createMany([
        [
            'type' => 'text',
            'order' => 1,
            'configuration' => [
                'type' => 'text',
                'name' => 'first_name',
                'label' => ['en' => 'First Name', 'de' => 'Vorname'],
            ],
        ],
        [
            'type' => 'email',
            'order' => 2,
            'configuration' => [
                'type' => 'email',
                'name' => 'email',
                'label' => ['en' => 'Email', 'de' => 'E-Mail'],
            ],
        ],
    ]);

    // Verify fields exist
    expect($form->fields)->toHaveCount(2);

    // Delete the form
    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->delete("/api/forms/{$form->id}");

    $response->assertStatus(200);

    // Verify form was deleted
    $this->assertDatabaseMissing('forms', [
        'id' => $form->id,
    ]);

    // Verify fields were also deleted (cascade)
    $this->assertDatabaseMissing('fields', [
        'form_id' => $form->id,
    ]);
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

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name.de']);
});

test('form validation requires configuration locales', function (): void {
    $formData = [
        'name' => [
            'en' => 'Contact Form',
            'de' => 'Kontaktformular',
        ],
        // Missing configuration.locales
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', $formData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['configuration']);
});

test('form validation requires at least one locale', function (): void {
    $formData = [
        'name' => [
            'en' => 'Contact Form',
            'de' => 'Kontaktformular',
        ],
        'configuration' => [
            'locales' => [], // Empty locales array
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', $formData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['configuration.locales']);
});

test('form validation accepts valid locales', function (): void {
    $formData = [
        'name' => [
            'en' => 'Contact Form',
            'de' => 'Test Formular',
        ],
        'description' => [
            'en' => 'Contact Form',
            'de' => 'Kontaktformular',
        ],
        'configuration' => [
            'locales' => ['en', 'de'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/forms', $formData);

    $response->assertStatus(201);
});

test('form validation rejects invalid locales', function (): void {
    $formData = [
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
    ])->post('/api/forms', $formData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['configuration.locales.1']);
});
