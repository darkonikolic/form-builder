<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Form;
use Illuminate\Database\Eloquent\Collection;

interface FormServiceInterface
{
    public function createForm(string $userId, array $data): Form;

    public function deleteForm(string $userId, string $formId): bool;

    public function getUserForm(string $userId, string $formId): Form;

    public function getUserForms(string $userId): Collection;

    public function updateForm(string $userId, string $formId, array $data): Form;
}
