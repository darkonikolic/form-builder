<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Form;
use Illuminate\Database\Eloquent\Collection;

class EloquentFormRepository implements FormRepositoryInterface
{
    public function create(array $data): Form
    {
        return Form::create($data);
    }

    public function delete(Form $form): bool
    {
        return $form->delete();
    }

    public function findById(string $id): ?Form
    {
        return Form::find($id);
    }

    public function findByIdOrFail(string $id): Form
    {
        return Form::findOrFail($id);
    }

    public function findByUserId(string $userId): Collection
    {
        return Form::where('user_id', $userId)->get();
    }

    public function update(Form $form, array $data): Form
    {
        $form->update($data);

        return $form->fresh();
    }

    public function userOwnsForm(string $userId, string $formId): bool
    {
        return Form::where('id', $formId)
            ->where('user_id', $userId)
            ->exists();
    }
}
