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
    $this->user = $this->createAuthenticatedUser();

    // Create a form for testing fields
    $this->form = $this->createFormForUser($this->user);
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
    $otherForm = $this->createFormForUser($this->user);
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

    $this->assertSuccessfulApiResponse($response, 'Fields retrieved successfully');

    // Should only return 2 fields for the specified form
    expect($response->json('data'))->toHaveCount(2);
});

test('user can create field', function (): void {
    $fieldData = $this->getFieldData('text', [
        'form_id' => $this->form->id,
        'order' => 1,
    ]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $fieldData);

    $this->assertCreatedApiResponse($response, 'Field created successfully');

    // Verify field was created in database
    $this->assertDatabaseHas('fields', [
        'form_id' => $this->form->id,
        'type' => 'text',
    ]);
});

test('user can create field without specifying order', function (): void {
    $fieldData = $this->getFieldData('email', [
        'form_id' => $this->form->id,
        // No order specified
    ]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $fieldData);

    $this->assertCreatedApiResponse($response, 'Field created successfully');

    // Verify field was created with auto-assigned order
    $this->assertDatabaseHas('fields', [
        'form_id' => $this->form->id,
        'type' => 'email',
    ]);

    // Verify the order was auto-assigned (should be 1 for first field)
    $createdField = Field::where('form_id', $this->form->id)
        ->where('type', 'email')
        ->first();
    $this->assertEquals(1, $createdField->order);
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

    $this->assertValidationError($response, ['configuration.label']);
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

    $this->assertValidationError($response, ['type']);
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

    $this->assertSuccessfulApiResponse($response, 'Field retrieved successfully');

    $response->assertJson([
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

    $this->assertNotFoundError($response, 'Field not found');
});

test('user cannot show field from another users form', function (): void {
    $otherUser = $this->createUser();
    $otherForm = $this->createFormForUser($otherUser);
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

    $this->assertNotFoundError($response, 'Form not found');
});

test('user gets 401 without authentication', function (): void {
    Auth::forgetGuards();

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get("/api/forms/{$this->form->id}/fields");

    $this->assertUnauthorizedError($response);
});

test('user gets 401 with invalid token', function (): void {
    // Clear any existing authentication
    Auth::forgetGuards();

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer invalid_token',
    ])->get("/api/forms/{$this->form->id}/fields");

    $this->assertUnauthorizedError($response);
});

test('user gets 401 when trying to access protected endpoint without auth', function (): void {
    Auth::forgetGuards();

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", []);

    $this->assertUnauthorizedError($response);
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
            'required' => false,
        ],
    ]);

    $updateData = [
        'configuration' => [
            'type' => 'textarea',
            'name' => 'updated_field',
            'label' => ['en' => 'Updated Label', 'de' => 'Aktualisiertes Etikett'],
            'required' => true,
            'rows' => 4,
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$this->form->id}/fields/{$field->id}", $updateData);

    $this->assertSuccessfulApiResponse($response, 'Field updated successfully');

    // Verify field was updated in database
    $this->assertDatabaseHas('fields', [
        'id' => $field->id,
        'form_id' => $this->form->id,
    ]);
});

test('user cannot update nonexistent field', function (): void {
    $fakeId = '550e8400-e29b-41d4-a716-446655440000';

    $updateData = [
        'configuration' => [
            'type' => 'textarea',
            'name' => 'updated_field',
            'label' => ['en' => 'Updated Label', 'de' => 'Aktualisiertes Etikett'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$this->form->id}/fields/{$fakeId}", $updateData);

    $this->assertNotFoundError($response, 'Field not found');
});

test('user cannot update field from another users form', function (): void {
    $otherUser = $this->createUser();
    $otherForm = $this->createFormForUser($otherUser);
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
        'configuration' => [
            'type' => 'textarea',
            'name' => 'updated_field',
            'label' => ['en' => 'Updated Label', 'de' => 'Aktualisiertes Etikett'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$otherForm->id}/fields/{$field->id}", $updateData);

    $this->assertNotFoundError($response, 'Form not found');
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
            'required' => false,
        ],
    ]);

    $invalidData = [
        'configuration' => [
            'type' => 'textarea',
            'name' => 'updated_field',
            'label' => [
                'en' => 'Updated Label',
                // Missing 'de' locale
            ],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put("/api/forms/{$this->form->id}/fields/{$field->id}", $invalidData);

    $this->assertValidationError($response, ['configuration.label.de']);
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
            'required' => true,
        ],
    ]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->delete("/api/forms/{$this->form->id}/fields/{$field->id}");

    $this->assertSuccessfulApiResponse($response, 'Field deleted successfully', false);

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

    $this->assertNotFoundError($response, 'Field not found');
});

test('user cannot delete field from another users form', function (): void {
    $otherUser = $this->createUser();
    $otherForm = $this->createFormForUser($otherUser);
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

    $this->assertNotFoundError($response, 'Form not found');
});

test('field validation requires name and label for all locales', function (): void {
    $fieldData = [
        'type' => 'text',
        'configuration' => [
            'type' => 'text',
            'name' => 'test_field',
            'label' => [
                'en' => 'Test Field',
                // Missing 'de' locale
            ],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $fieldData);

    $this->assertValidationError($response, ['configuration.label.de']);
});

test('field validation requires valid field type', function (): void {
    $fieldData = [
        'type' => 'invalid_type',
        'configuration' => [
            'type' => 'invalid_type',
            'name' => 'test_field',
            'label' => ['en' => 'Test Field'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $fieldData);

    $this->assertValidationError($response, ['type']);
});

test('field validation accepts valid field types', function (): void {
    $validTypes = ['text', 'number', 'textarea', 'select', 'checkbox', 'radio', 'email', 'file', 'date', 'time', 'datetime-local', 'url', 'tel', 'search', 'color', 'range', 'hidden'];

    foreach ($validTypes as $type) {
        $fieldData = $this->getFieldData($type, [
            'form_id' => $this->form->id,
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post("/api/forms/{$this->form->id}/fields", $fieldData);

        $response->assertStatus(201);

        // Verify the response contains the correct field type
        $response->assertJson([
            'data' => [
                'type' => $type,
                'configuration' => [
                    'type' => $type,
                ],
            ],
        ]);
    }
});

test('field validation requires order to be non-negative', function (): void {
    $fieldData = [
        'type' => 'text',
        'order' => -1,
        'configuration' => [
            'type' => 'text',
            'name' => 'test_field',
            'label' => ['en' => 'Test Field'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $fieldData);

    $this->assertValidationError($response, ['order']);
});

test('field validation accepts validation rules', function (): void {
    $fieldData = [
        'type' => 'text',
        'configuration' => [
            'type' => 'text',
            'name' => 'test_field',
            'label' => ['en' => 'Test Field', 'de' => 'Test Feld'],
            'validation_rules' => ['required', 'min:3', 'max:50'],
        ],
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post("/api/forms/{$this->form->id}/fields", $fieldData);

    $this->assertCreatedApiResponse($response, 'Field created successfully');
});
