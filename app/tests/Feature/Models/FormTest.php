<?php

declare(strict_types=1);

use App\Models\Field;
use App\Models\Form;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Form Management', function (): void {
    beforeEach(function (): void {
        $this->user = $this->createUser();
    });

    describe('Form CRUD Operations', function (): void {
        it('can create form', function (): void {
            $formData = $this->getFormData([
                'user_id' => $this->user->id,
            ]);

            $form = Form::create($formData);

            $this->assertDatabaseHas('forms', [
                'id' => $form->id,
                'user_id' => $this->user->id,
                'is_active' => true,
            ]);
            $this->assertIsString($form->id);
            $this->assertEquals('Contact Form', $form->name['en']);
            $this->assertEquals('A contact form for customer inquiries', $form->description['en']);
            $this->assertTrue($form->is_active);
        });

        it('can read form', function (): void {
            $form = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $retrievedForm = Form::find($form->id);

            $this->assertNotNull($retrievedForm);
            $this->assertEquals($this->user->id, $retrievedForm->user_id);
            $this->assertTrue($retrievedForm->is_active);
        });

        it('can update form', function (): void {
            $form = Form::factory()->create([
                'user_id' => $this->user->id,
                'is_active' => false,
            ]);

            $form->update([
                'name' => ['en' => 'Updated Name', 'de' => 'Aktualisierter Name'],
                'description' => ['en' => 'Updated description', 'de' => 'Aktualisierte Beschreibung'],
                'is_active' => true,
            ]);

            $this->assertDatabaseHas('forms', [
                'id' => $form->id,
                'user_id' => $this->user->id,
                'is_active' => true,
            ]);

            $updatedForm = Form::find($form->id);
            $this->assertEquals('Updated Name', $updatedForm->name['en']);
            $this->assertEquals('Updated description', $updatedForm->description['en']);
            $this->assertTrue($updatedForm->is_active);
        });

        it('can delete form', function (): void {
            $form = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $formId = $form->id;
            $form->delete();

            $this->assertDatabaseMissing('forms', ['id' => $formId]);
            $this->assertNull(Form::find($formId));
        });
    });

    describe('Form Fields Relationship', function (): void {
        it('form has fields relationship', function (): void {
            $form = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $field = Field::factory()->create([
                'form_id' => $form->id,
            ]);

            // Test the relationship by checking the foreign key and querying directly
            $this->assertEquals($form->id, $field->form_id);
            $this->assertTrue($form->fields->contains($field));
        });

        it('can create form with multiple fields', function (): void {
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

            $this->assertEquals(2, $form->fields->count());
            $this->assertTrue($form->fields->contains($field1));
            $this->assertTrue($form->fields->contains($field2));
        });
    });

    describe('Form User Relationship', function (): void {
        it('form belongs to user', function (): void {
            $form = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $this->assertEquals($this->user->id, $form->user->id);
            $this->assertEquals($this->user->name, $form->user->name);
        });

        it('user can have multiple forms', function (): void {
            $form1 = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $form2 = Form::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $this->assertEquals(2, $this->user->forms->count());
            $this->assertTrue($this->user->forms->contains($form1));
            $this->assertTrue($this->user->forms->contains($form2));
        });
    });
});
