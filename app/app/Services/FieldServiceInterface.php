<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Field;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface FieldServiceInterface
{
    public function createField(string $userId, string $formId, array $data): Field;

    public function deleteField(string $userId, string $formId, string $fieldId): bool;

    public function fieldBelongsToUserForm(User $user, string $formId, string $fieldId): bool;

    public function getFormField(string $userId, string $formId, string $fieldId): Field;

    public function getFormFields(string $userId, string $formId): Collection;

    public function updateField(string $userId, string $formId, string $fieldId, array $data): Field;
}
