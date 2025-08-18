<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Form;
use App\Models\User;
use App\Repositories\FormRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FormService implements FormServiceInterface
{
    public function __construct(
        private FormRepositoryInterface $formRepository,
    ) {
    }

    /**
     * Create a new form for a user.
     */
    public function createForm(string $userId, array $formData): Form
    {
        $formData['user_id'] = $userId;

        return $this->formRepository->create($formData);
    }

    /**
     * Delete a form for a user.
     */
    public function deleteForm(string $userId, string $formId): bool
    {
        if (!$this->formRepository->userOwnsForm($userId, $formId)) {
            throw new \App\Exceptions\ResourceNotFoundException('Form not found');
        }

        $form = $this->formRepository->findByIdOrFail($formId);

        return $this->formRepository->delete($form);
    }

    /**
     * Check if a form belongs to a user.
     */
    public function formBelongsToUser(User $user, string $formId): bool
    {
        return $this->formRepository->userOwnsForm($user->id, $formId);
    }

    /**
     * Get a specific form for a user.
     */
    public function getUserForm(string $userId, string $formId): Form
    {
        if (!$this->formRepository->userOwnsForm($userId, $formId)) {
            throw new \App\Exceptions\ResourceNotFoundException('Form not found');
        }

        return $this->formRepository->findByIdOrFail($formId);
    }

    /**
     * Get all forms for a user.
     */
    public function getUserForms(string $userId): Collection
    {
        return $this->formRepository->findByUserId($userId);
    }

    /**
     * Get a form with its fields for a user.
     */
    public function getUserFormWithFields(User $user, string $formId): Form
    {
        if (!$this->formRepository->userOwnsForm($user->id, $formId)) {
            throw new \App\Exceptions\ResourceNotFoundException('Form not found');
        }

        return $this->formRepository->findByIdOrFail($formId)->load('fields');
    }

    /**
     * Update a form for a user.
     */
    public function updateForm(string $userId, string $formId, array $formData): Form
    {
        if (!$this->formRepository->userOwnsForm($userId, $formId)) {
            throw new \App\Exceptions\ResourceNotFoundException('Form not found');
        }

        $form = $this->formRepository->findByIdOrFail($formId);

        return $this->formRepository->update($form, $formData);
    }
}
