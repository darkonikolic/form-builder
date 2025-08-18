<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ServerException;
use App\Models\Form;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class FormService
{
    /**
     * Create a new form for a user.
     *
     * @throws ServerException
     */
    public function createUserForm(User $user, array $formData): Form
    {
        try {
            return $user->forms()->create($formData);
        } catch (QueryException|ValidationException $e) {
            throw new ServerException('Failed to create form: ' . $e->getMessage());
        }
    }

    /**
     * Delete a form for a user.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
     */
    public function deleteUserForm(User $user, string $formId): bool
    {
        try {
            $form = $user->forms()->findOrFail($formId);

            return $form->delete();
        } catch (ModelNotFoundException $e) {
            throw new ResourceNotFoundException('Form not found');
        } catch (QueryException $e) {
            throw new ServerException('Failed to delete form: ' . $e->getMessage());
        }
    }

    /**
     * Check if form belongs to user.
     */
    public function formBelongsToUser(User $user, string $formId): bool
    {
        return $user->forms()->where('id', $formId)->exists();
    }

    /**
     * Get all forms for a user.
     *
     * @throws ServerException
     */
    public function getUserForms(User $user): Collection
    {
        try {
            return $user->forms()->orderBy('created_at', 'desc')->get();
        } catch (QueryException $e) {
            throw new ServerException('Failed to retrieve forms: ' . $e->getMessage());
        }
    }

    /**
     * Get a specific form with fields for a user.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
     */
    public function getUserFormWithFields(User $user, string $formId): Form
    {
        try {
            return $user->forms()->with('fields')->findOrFail($formId);
        } catch (ModelNotFoundException $e) {
            throw new ResourceNotFoundException('Form not found');
        } catch (QueryException $e) {
            throw new ServerException('Failed to retrieve form: ' . $e->getMessage());
        }
    }

    /**
     * Update a form for a user.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
     */
    public function updateUserForm(User $user, string $formId, array $formData): Form
    {
        try {
            $form = $user->forms()->findOrFail($formId);
            $form->update($formData);

            return $form->fresh();
        } catch (ModelNotFoundException $e) {
            throw new ResourceNotFoundException('Form not found');
        } catch (QueryException|ValidationException $e) {
            throw new ServerException('Failed to update form: ' . $e->getMessage());
        }
    }
}
