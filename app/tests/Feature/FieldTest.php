<?php

declare(strict_types=1);

use App\Models\Field;
use App\Models\Form;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Field Management', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
    });

    describe('Field CRUD Operations', function (): void {
        it('can create field', function (): void {
            $form = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $fieldData = [
                'form_id' => $form->id,
                'type' => 'text',
                'order' => 1,
                'configuration' => [
                    'type' => 'text',
                    'name' => 'test_field',
                    'label' => ['en' => 'Test Field', 'de' => 'Test Feld'],
                    'required' => true,
                ],
            ];

            $field = Field::create($fieldData);

            $this->assertDatabaseHas('fields', [
                'id' => $field->id,
                'form_id' => $form->id,
            ]);
            $this->assertIsString($field->id);
            $this->assertEquals($form->id, $field->form_id);
            $this->assertEquals('text', $field->configuration['type']);
            $this->assertEquals('Test Field', $field->configuration['label']['en']);
            $this->assertTrue($field->configuration['required']);
        });

        it('can read field', function (): void {
            $form = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $field = Field::factory()->create([
                'form_id' => $form->id,
            ]);

            $retrievedField = Field::find($field->id);

            $this->assertNotNull($retrievedField);
            $this->assertEquals($form->id, $retrievedField->form_id);
            $this->assertEquals($field->configuration['type'], $retrievedField->configuration['type']);
        });

        it('can update field', function (): void {
            $form = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $field = Field::factory()->create([
                'form_id' => $form->id,
            ]);

            $field->update([
                'configuration' => [
                    'type' => 'textarea',
                    'name' => 'updated_field',
                    'label' => ['en' => 'Updated Label', 'de' => 'Aktualisiertes Etikett'],
                    'required' => true,
                    'rows' => 4,
                ],
            ]);

            $this->assertDatabaseHas('fields', [
                'id' => $field->id,
                'form_id' => $form->id,
            ]);

            $updatedField = Field::find($field->id);
            $this->assertEquals('textarea', $updatedField->configuration['type']);
            $this->assertEquals('Updated Label', $updatedField->configuration['label']['en']);
            $this->assertTrue($updatedField->configuration['required']);
        });

        it('can delete field', function (): void {
            $form = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $field = Field::factory()->create([
                'form_id' => $form->id,
            ]);

            $fieldId = $field->id;
            $field->delete();

            $this->assertDatabaseMissing('fields', ['id' => $fieldId]);
            $this->assertNull(Field::find($fieldId));
        });
    });

    describe('Field Form Relationship', function (): void {
        it('field belongs to form', function (): void {
            $form = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $field = Field::factory()->create([
                'form_id' => $form->id,
            ]);

            $this->assertTrue($field->form->is($form));
            $this->assertEquals($form->id, $field->form_id);
        });

        it('form can have multiple fields', function (): void {
            $form = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $fields = Field::factory()->count(3)->create([
                'form_id' => $form->id,
            ]);

            $this->assertEquals(3, $form->fields()->count());
            $this->assertTrue($form->fields->contains($fields->first()));
        });
    });

    describe('Field Validation', function (): void {
        it('validates required configuration', function (): void {
            $form = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $this->expectException(\Exception::class);

            Field::create([
                'form_id' => $form->id,
                'type' => 'text',
                'order' => 1,
                'configuration' => [
                    // Missing required 'name' field
                    'type' => 'text',
                    'label' => ['en' => 'Test Field', 'de' => 'Test Feld'],
                ],
            ]);
        });

        it('validates field type', function (): void {
            $form = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $this->expectException(\Exception::class);

            Field::create([
                'form_id' => $form->id,
                'type' => 'invalid_type',
                'order' => 1,
                'configuration' => [
                    'type' => 'invalid_type',
                    'name' => 'test_field',
                    'label' => ['en' => 'Test Field', 'de' => 'Test Feld'],
                ],
            ]);
        });
    });

    describe('Field Ordering', function (): void {
        it('maintains field order', function (): void {
            $form = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $field1 = Field::factory()->create([
                'form_id' => $form->id,
                'order' => 1,
            ]);

            $field2 = Field::factory()->create([
                'form_id' => $form->id,
                'order' => 2,
            ]);

            $field3 = Field::factory()->create([
                'form_id' => $form->id,
                'order' => 3,
            ]);

            $orderedFields = $form->fields()->get();

            $this->assertEquals($field1->id, $orderedFields[0]->id);
            $this->assertEquals($field2->id, $orderedFields[1]->id);
            $this->assertEquals($field3->id, $orderedFields[2]->id);
        });
    });
});
