<?php

declare(strict_types=1);

use App\Models\Field;
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

    // Create a form for testing fields
    $this->form = Form::create([
        'user_id' => $this->user->id,
        'name' => ['en' => 'Test Form', 'de' => 'Test Formular'],
        'description' => ['en' => 'Test Description', 'de' => 'Test Beschreibung'],
        'is_active' => true,
        'configuration' => ['locales' => ['en', 'de']],
    ]);
});

test('user can list fields for a form', function (): void {
    // Create some fields for the form
    Field::create([
        'form_id' => $this->form->id,
        'type' => 'text',
        'order' => 1,
        'configuration' => [
            'type' => 'text',
            'name' => 'first_name',
            'label' => ['en' => 'First Name', 'de' => 'Vorname'],
            'required' => true,
        ],
    ]);

    Field::create([
        'form_id' => $this->form->id,
        'type' => 'email',
        'order' => 2,
        'configuration' => [
            'type' => 'email',
            'name' => 'email',
            'label' => ['en' => 'Email', 'de' => 'E-Mail'],
            'required' => true,
        ],
    ]);

    // Create fields for another form (should not appear)
    $otherForm = Form::create([
        'user_id' => $this->user->id,
        'name' => ['en' => 'Other Form', 'de' => 'Anderes Formular'],
        'description' => ['en' => 'Other Description', 'de' => 'Andere Beschreibung'],
        'is_active' => true,
        'configuration' => ['locales' => ['en', 'de']],
    ]);

    Field::create([
        'form_id' => $otherForm->id,
        'type' => 'text',
        'order' => 1,
        'configuration' => [
            'type' => 'text',
            'name' => 'other_field',
            'label' => ['en' => 'Other Field', 'de' => 'Anderes Feld'],
        ],
    ]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get("/api/forms/{$this->form->id}/fields");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'form_id',
                    'type',
                    'order',
                    'configuration',
                    'created_at',
                    'updated_at',
                ],
            ],
            'message',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Fields retrieved successfully',
        ]);

    // Should only return 2 fields for the specified form
    expect($response->json('data'))->toHaveCount(2);

    // Should be ordered by order field
    expect($response->json('data.0.order'))->toBe(1);
    expect($response->json('data.1.order'))->toBe(2);
});

test('user can create field', function (): void {
    $fieldData = [
        'type' => 'text',
        'order' => 1,
        'configuration' => [
            'type' => 'text',
            'name' => 'first_name',
            'label' => ['en' => 'First Name', 'de' => 'Vorname'],
            'required' => true,
            'placeholder' => [
                'en' => 'Enter your first name',
                'de' => 'Geben Sie Ihren Vornamen ein',
            ],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $fieldData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'form_id',
                'type',
                'order',
                'configuration',
                'created_at',
                'updated_at',
            ],
            'message',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Field created successfully',
            'data' => [
                'form_id' => $this->form->id,
                'type' => $fieldData['type'],
                'order' => $fieldData['order'],
                'configuration' => $fieldData['configuration'],
            ],
        ]);

    // Verify field was saved to database
    $this->assertDatabaseHas('fields', [
        'form_id' => $this->form->id,
        'type' => 'text',
        'order' => 1,
    ]);

    $field = Field::where('form_id', $this->form->id)->first();
    expect($field->configuration['name'])->toBe('first_name');
    expect($field->configuration['label']['en'])->toBe('First Name');
    expect($field->configuration['label']['de'])->toBe('Vorname');
});

test('user can create field without specifying order', function (): void {
    // Create a field first to set the max order
    Field::create([
        'form_id' => $this->form->id,
        'type' => 'text',
        'order' => 5,
        'configuration' => [
            'type' => 'text',
            'name' => 'existing_field',
            'label' => ['en' => 'Existing Field', 'de' => 'Existierendes Feld'],
        ],
    ]);

    $fieldData = [
        'type' => 'email',
        'configuration' => [
            'type' => 'email',
            'name' => 'email',
            'label' => ['en' => 'Email', 'de' => 'E-Mail'],
            'required' => true,
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $fieldData);

    $response->assertStatus(201);

    // Should automatically set order to max + 1 (6)
    $field = Field::where('form_id', $this->form->id)->where('type', 'email')->first();
    expect($field->order)->toBe(6);
});

test('user cannot create field with invalid data', function (): void {
    $invalidData = [
        'type' => 'text',
        'configuration' => [
            'type' => 'text',
            'name' => 'first_name',
            // Missing 'label' locale
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $invalidData);

    $response->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
            'errors' => [
                'configuration.label',
            ],
        ])
        ->assertJson([
            'success' => false,
            'message' => 'Validation failed',
        ]);
});

test('user cannot create field with invalid type', function (): void {
    $invalidData = [
        'type' => 'invalid_type',
        'configuration' => [
            'type' => 'text',
            'name' => 'first_name',
            'label' => ['en' => 'First Name', 'de' => 'Vorname'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $invalidData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type']);
});

test('user can show field', function (): void {
    $field = Field::create([
        'form_id' => $this->form->id,
        'type' => 'text',
        'order' => 1,
        'configuration' => [
            'type' => 'text',
            'name' => 'first_name',
            'label' => ['en' => 'First Name', 'de' => 'Vorname'],
            'required' => true,
        ],
    ]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get("/api/forms/{$this->form->id}/fields/{$field->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'form_id',
                'type',
                'order',
                'configuration',
                'created_at',
                'updated_at',
            ],
            'message',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Field retrieved successfully',
            'data' => [
                'id' => $field->id,
                'form_id' => $this->form->id,
                'type' => 'text',
            ],
        ]);
});

test('user cannot show nonexistent field', function (): void {
    $fakeId = '550e8400-e29b-41d4-a716-446655440000';

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get("/api/forms/{$this->form->id}/fields/{$fakeId}");

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Form or field not found',
        ]);
});

test('user cannot show field from another users form', function (): void {
    $otherUser = User::factory()->create();
    $otherForm = Form::create([
        'user_id' => $otherUser->id,
        'name' => ['en' => 'Other Form', 'de' => 'Anderes Formular'],
        'description' => ['en' => 'Other Description', 'de' => 'Andere Beschreibung'],
        'is_active' => true,
        'configuration' => ['locales' => ['en', 'de']],
    ]);

    $field = Field::create([
        'form_id' => $otherForm->id,
        'type' => 'text',
        'order' => 1,
        'configuration' => [
            'type' => 'text',
            'name' => 'other_field',
            'label' => ['en' => 'Other Field', 'de' => 'Anderes Feld'],
        ],
    ]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get("/api/forms/{$otherForm->id}/fields/{$field->id}");

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Form or field not found',
        ]);
});

test('user gets 401 without authentication', function (): void {
    // Test without any authentication - should get 401
    Auth::forgetGuards();

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get("/api/forms/{$this->form->id}/fields");

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
    ])->get("/api/forms/{$this->form->id}/fields");

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
    ])->post("/api/forms/{$this->form->id}/fields", [
        'type' => 'text',
        'configuration' => [
            'type' => 'text',
            'name' => 'test_field',
            'label' => ['en' => 'Test Field', 'de' => 'Test Feld'],
        ],
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
});

test('user can update field', function (): void {
    $field = Field::create([
        'form_id' => $this->form->id,
        'type' => 'text',
        'order' => 1,
        'configuration' => [
            'type' => 'text',
            'name' => 'first_name',
            'label' => ['en' => 'First Name', 'de' => 'Vorname'],
            'required' => true,
        ],
    ]);

    $updateData = [
        'type' => 'email',
        'order' => 2,
        'configuration' => [
            'type' => 'email',
            'name' => 'email',
            'label' => ['en' => 'Email', 'de' => 'E-Mail'],
            'required' => false,
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$this->form->id}/fields/{$field->id}", $updateData);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'form_id',
                'type',
                'order',
                'configuration',
                'created_at',
                'updated_at',
            ],
            'message',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Field updated successfully',
            'data' => [
                'type' => $updateData['type'],
                'order' => $updateData['order'],
                'configuration' => $updateData['configuration'],
            ],
        ]);

    // Verify field was updated in database
    $this->assertDatabaseHas('fields', [
        'id' => $field->id,
        'type' => 'email',
        'order' => 2,
    ]);

    $updatedField = Field::find($field->id);
    expect($updatedField->configuration['name'])->toBe('email');
    expect($updatedField->configuration['required'])->toBe(false);
});

test('user cannot update nonexistent field', function (): void {
    $fakeId = '550e8400-e29b-41d4-a716-446655440000';
    $updateData = [
        'type' => 'email',
        'configuration' => [
            'type' => 'email',
            'name' => 'email',
            'label' => ['en' => 'Email', 'de' => 'E-Mail'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$this->form->id}/fields/{$fakeId}", $updateData);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Form or field not found',
        ]);
});

test('user cannot update field from another users form', function (): void {
    $otherUser = User::factory()->create();
    $otherForm = Form::create([
        'user_id' => $otherUser->id,
        'name' => ['en' => 'Other Form', 'de' => 'Anderes Formular'],
        'description' => ['en' => 'Other Description', 'de' => 'Andere Beschreibung'],
        'is_active' => true,
        'configuration' => ['locales' => ['en', 'de']],
    ]);

    $field = Field::create([
        'form_id' => $otherForm->id,
        'type' => 'text',
        'order' => 1,
        'configuration' => [
            'type' => 'text',
            'name' => 'other_field',
            'label' => ['en' => 'Other Field', 'de' => 'Anderes Feld'],
        ],
    ]);

    $updateData = [
        'type' => 'email',
        'configuration' => [
            'type' => 'email',
            'name' => 'email',
            'label' => ['en' => 'Email', 'de' => 'E-Mail'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$otherForm->id}/fields/{$field->id}", $updateData);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Form or field not found',
        ]);
});

test('user cannot update field with invalid data', function (): void {
    $field = Field::create([
        'form_id' => $this->form->id,
        'type' => 'text',
        'order' => 1,
        'configuration' => [
            'type' => 'text',
            'name' => 'first_name',
            'label' => ['en' => 'First Name', 'de' => 'Vorname'],
        ],
    ]);

    $invalidData = [
        'configuration' => [
            'label' => [
                'en' => 'Updated Label',
                // Missing 'de' locale
            ],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$this->form->id}/fields/{$field->id}", $invalidData);

    $response->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
            'errors' => [
                'configuration.label.de',
            ],
        ])
        ->assertJson([
            'success' => false,
            'message' => 'Validation failed',
        ]);
});

test('user can delete field', function (): void {
    $field = Field::create([
        'form_id' => $this->form->id,
        'type' => 'text',
        'order' => 1,
        'configuration' => [
            'type' => 'text',
            'name' => 'first_name',
            'label' => ['en' => 'First Name', 'de' => 'Vorname'],
        ],
    ]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->delete("/api/forms/{$this->form->id}/fields/{$field->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Field deleted successfully',
        ]);

    // Verify field was deleted from database
    $this->assertDatabaseMissing('fields', [
        'id' => $field->id,
    ]);
});

test('user cannot delete nonexistent field', function (): void {
    $fakeId = '550e8400-e29b-41d4-a716-446655440000';

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->delete("/api/forms/{$this->form->id}/fields/{$fakeId}");

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Form or field not found',
        ]);
});

test('user cannot delete field from another users form', function (): void {
    $otherUser = User::factory()->create();
    $otherForm = Form::create([
        'user_id' => $otherUser->id,
        'name' => ['en' => 'Other Form', 'de' => 'Anderes Formular'],
        'description' => ['en' => 'Other Description', 'de' => 'Andere Beschreibung'],
        'is_active' => true,
        'configuration' => ['locales' => ['en', 'de']],
    ]);

    $field = Field::create([
        'form_id' => $otherForm->id,
        'type' => 'text',
        'order' => 1,
        'configuration' => [
            'type' => 'text',
            'name' => 'other_field',
            'label' => ['en' => 'Other Field', 'de' => 'Anderes Feld'],
        ],
    ]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->delete("/api/forms/{$otherForm->id}/fields/{$field->id}");

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Form or field not found',
        ]);
});

test('field validation requires name and label for all locales', function (): void {
    $fieldData = [
        'type' => 'text',
        'configuration' => [
            'type' => 'text',
            'name' => 'first_name',
            'label' => [
                'en' => 'First Name',
                // Missing 'de' locale
            ],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $fieldData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['configuration.label.de']);
});

test('field validation requires valid field type', function (): void {
    $fieldData = [
        'type' => 'invalid_type',
        'configuration' => [
            'type' => 'text',
            'name' => 'first_name',
            'label' => ['en' => 'First Name', 'de' => 'Vorname'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $fieldData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type']);
});

test('field validation accepts valid field types', function (): void {
    $validTypes = ['text', 'email', 'password', 'number', 'textarea', 'checkbox', 'file', 'date', 'time', 'datetime-local', 'url', 'tel', 'search', 'color', 'range', 'hidden'];

    foreach ($validTypes as $type) {
        $fieldData = [
            'type' => $type,
            'configuration' => [
                'type' => $type,
                'name' => "field_{$type}",
                'label' => ['en' => ucfirst($type), 'de' => ucfirst($type)],
            ],
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post("/api/forms/{$this->form->id}/fields", $fieldData);

        $response->assertStatus(201);
    }

    // Test select and radio fields separately as they require options
    $selectFieldData = [
        'type' => 'select',
        'configuration' => [
            'type' => 'select',
            'name' => 'field_select',
            'label' => ['en' => 'Select', 'de' => 'Auswahl'],
            'options' => [
                ['value' => 'option1', 'label' => ['en' => 'Option 1', 'de' => 'Option 1']],
                ['value' => 'option2', 'label' => ['en' => 'Option 2', 'de' => 'Option 2']],
            ],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $selectFieldData);

    $response->assertStatus(201);

    $radioFieldData = [
        'type' => 'radio',
        'configuration' => [
            'type' => 'radio',
            'name' => 'field_radio',
            'label' => ['en' => 'Radio', 'de' => 'Radio'],
            'options' => [
                ['value' => 'radio1', 'label' => ['en' => 'Radio 1', 'de' => 'Radio 1']],
                ['value' => 'radio2', 'label' => ['en' => 'Radio 2', 'de' => 'Radio 2']],
            ],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $radioFieldData);

    $response->assertStatus(201);
});

test('field validation requires order to be non-negative', function (): void {
    $fieldData = [
        'type' => 'text',
        'order' => -1,
        'configuration' => [
            'type' => 'text',
            'name' => 'first_name',
            'label' => ['en' => 'First Name', 'de' => 'Vorname'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $fieldData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['order']);
});

test('field validation accepts validation rules', function (): void {
    $fieldData = [
        'type' => 'text',
        'configuration' => [
            'type' => 'text',
            'name' => 'first_name',
            'label' => ['en' => 'First Name', 'de' => 'Vorname'],
            'required' => true,
        ],
        'validation_rules' => [
            'required' => [
                'error_messages' => [
                    'en' => 'This field is required',
                    'de' => 'Dieses Feld ist erforderlich',
                ],
            ],
            'min' => [
                'error_messages' => [
                    'en' => 'Minimum length is 2 characters',
                    'de' => 'Mindestlänge ist 2 Zeichen',
                ],
            ],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $fieldData);

    $response->assertStatus(201);

    $field = Field::where('form_id', $this->form->id)->where('type', 'text')->first();
    expect($field->validation_rules)->toHaveCount(2);
    expect($field->validation_rules['required']['error_messages']['en'])->toBe('This field is required');
    expect($field->validation_rules['min']['error_messages']['de'])->toBe('Mindestlänge ist 2 Zeichen');
});
