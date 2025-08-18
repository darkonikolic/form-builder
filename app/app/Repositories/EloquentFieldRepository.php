<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Field;
use Illuminate\Database\Eloquent\Collection;

class EloquentFieldRepository implements FieldRepositoryInterface
{
    public function create(array $data): Field
    {
        return Field::create($data);
    }

    public function delete(Field $field): bool
    {
        return $field->delete();
    }

    public function findByFormId(string $formId): Collection
    {
        return Field::where('form_id', $formId)
            ->orderBy('order')
            ->get();
    }

    public function findById(string $id): ?Field
    {
        return Field::find($id);
    }

    public function findByIdOrFail(string $id): Field
    {
        return Field::findOrFail($id);
    }

    public function getNextOrder(string $formId): int
    {
        $maxOrder = Field::where('form_id', $formId)->max('order');

        return ($maxOrder ?? 0) + 1;
    }

    public function update(Field $field, array $data): Field
    {
        $field->update($data);

        return $field->fresh();
    }

    public function userOwnsField(string $userId, string $fieldId): bool
    {
        return Field::where('id', $fieldId)
            ->whereHas('form', function ($query) use ($userId): void {
                $query->where('user_id', $userId);
            })
            ->exists();
    }
}
