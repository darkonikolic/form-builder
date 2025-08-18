<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Field;
use Illuminate\Database\Eloquent\Collection;

interface FieldRepositoryInterface
{
    public function create(array $data): Field;

    public function delete(Field $field): bool;

    public function findByFormId(string $formId): Collection;

    public function findById(string $id): ?Field;

    public function findByIdOrFail(string $id): Field;

    public function getNextOrder(string $formId): int;

    public function update(Field $field, array $data): Field;

    public function userOwnsField(string $userId, string $fieldId): bool;
}
