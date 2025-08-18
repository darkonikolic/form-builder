<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Field;
use App\Models\Form;
use App\Models\User;
use App\Repositories\FieldRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FieldService implements FieldServiceInterface
{
    public function __construct(
        private FieldRepositoryInterface $fieldRepository,
        private FormService $formService,
    ) {
    }

    /**
     * Create a new field for a form.
     */
    public function createField(string $userId, string $formId, array $fieldData): Field
    {
        // First check if user owns the form
        $this->formService->getUserForm($userId, $formId);

        $fieldData['form_id'] = $formId;

        if (!isset($fieldData['order'])) {
            $fieldData['order'] = $this->fieldRepository->getNextOrder($formId);
        }

        return $this->fieldRepository->create($fieldData);
    }

    /**
     * Delete a field from a form.
     */
    public function deleteField(string $userId, string $formId, string $fieldId): bool
    {
        // First check if field exists
        $field = $this->fieldRepository->findById($fieldId);
        if (!$field) {
            throw new ResourceNotFoundException('Field not found');
        }

        // Then check if user owns the form
        $this->formService->getUserForm($userId, $formId);

        // Finally check if field belongs to the form
        if ($field->form_id !== $formId) {
            throw new ResourceNotFoundException('Field not found');
        }

        return $this->fieldRepository->delete($field);
    }

    /**
     * Check if a field belongs to a user's form.
     */
    public function fieldBelongsToUserForm(User $user, string $formId, string $fieldId): bool
    {
        // First check if user owns the form
        try {
            $this->formService->getUserForm($user->id, $formId);
        } catch (\App\Exceptions\ResourceNotFoundException $e) {
            return false;
        }

        // Then check if field exists and belongs to the form
        $field = $this->fieldRepository->findById($fieldId);

        return $field && $field->form_id === $formId;
    }

    /**
     * Get a specific field from a form.
     */
    public function getFormField(string $userId, string $formId, string $fieldId): Field
    {
        // First check if field exists
        $field = $this->fieldRepository->findById($fieldId);
        if (!$field) {
            throw new ResourceNotFoundException('Field not found');
        }

        // Then check if user owns the form
        $this->formService->getUserForm($userId, $formId);

        // Finally check if field belongs to the form
        if ($field->form_id !== $formId) {
            throw new ResourceNotFoundException('Field not found');
        }

        return $field;
    }

    /**
     * Get all fields for a form.
     */
    public function getFormFields(string $userId, string $formId): Collection
    {
        // First check if user owns the form
        $this->formService->getUserForm($userId, $formId);

        return $this->fieldRepository->findByFormId($formId);
    }

    /**
     * Update a field in a form.
     */
    public function updateField(string $userId, string $formId, string $fieldId, array $fieldData): Field
    {
        // First check if field exists
        $field = $this->fieldRepository->findById($fieldId);
        if (!$field) {
            throw new ResourceNotFoundException('Field not found');
        }

        // Then check if user owns the form
        $this->formService->getUserForm($userId, $formId);

        // Finally check if field belongs to the form
        if ($field->form_id !== $formId) {
            throw new ResourceNotFoundException('Field not found');
        }

        return $this->fieldRepository->update($field, $fieldData);
    }
}
