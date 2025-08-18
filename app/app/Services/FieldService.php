<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ServerException;
use App\Models\Field;
use App\Models\Form;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class FieldService
{
    /**
     * Create a new field for a form.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
     */
    public function createFormField(User $user, string $formId, array $fieldData): Field
    {
        try {
            $form = $user->forms()->findOrFail($formId);

            if (!isset($fieldData['order'])) {
                $fieldData['order'] = $form->fields()->max('order') + 1;
            }

            return $form->fields()->create($fieldData);
        } catch (ModelNotFoundException $e) {
            throw new ResourceNotFoundException('Form not found');
        } catch (QueryException|ValidationException $e) {
            throw new ServerException('Failed to create field: ' . $e->getMessage());
        }
    }

    /**
     * Delete a field from a form.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
     */
    public function deleteFormField(User $user, string $formId, string $fieldId): bool
    {
        try {
            $form = $user->forms()->findOrFail($formId);
            $field = $form->fields()->findOrFail($fieldId);

            return $field->delete();
        } catch (ModelNotFoundException $e) {
            throw new ResourceNotFoundException('Form or field not found');
        } catch (QueryException $e) {
            throw new ServerException('Failed to delete field: ' . $e->getMessage());
        }
    }

    /**
     * Check if field belongs to user's form.
     */
    public function fieldBelongsToUserForm(User $user, string $formId, string $fieldId): bool
    {
        return $user->forms()
            ->where('id', $formId)
            ->whereHas('fields', function ($query) use ($fieldId): void {
                $query->where('id', $fieldId);
            })
            ->exists();
    }

    /**
     * Get a specific field from a form.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
     */
    public function getFormField(User $user, string $formId, string $fieldId): Field
    {
        try {
            $form = $user->forms()->findOrFail($formId);

            return $form->fields()->findOrFail($fieldId);
        } catch (ModelNotFoundException $e) {
            throw new ResourceNotFoundException('Form or field not found');
        } catch (QueryException $e) {
            throw new ServerException('Failed to retrieve field: ' . $e->getMessage());
        }
    }

    /**
     * Get all fields for a form.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
     */
    public function getFormFields(User $user, string $formId): Collection
    {
        try {
            $form = $user->forms()->findOrFail($formId);

            return $form->fields()->orderBy('order')->get();
        } catch (ModelNotFoundException $e) {
            throw new ResourceNotFoundException('Form not found');
        } catch (QueryException $e) {
            throw new ServerException('Failed to retrieve fields: ' . $e->getMessage());
        }
    }

    /**
     * Update a field in a form.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
     */
    public function updateFormField(User $user, string $formId, string $fieldId, array $fieldData): Field
    {
        try {
            $form = $user->forms()->findOrFail($formId);
            $field = $form->fields()->findOrFail($fieldId);
            $field->update($fieldData);

            return $field->fresh();
        } catch (ModelNotFoundException $e) {
            throw new ResourceNotFoundException('Form or field not found');
        } catch (QueryException|ValidationException $e) {
            throw new ServerException('Failed to update field: ' . $e->getMessage());
        }
    }
}
