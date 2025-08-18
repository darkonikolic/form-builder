<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Form;
use Illuminate\Database\Eloquent\Collection;

interface FormRepositoryInterface
{
    public function create(array $data): Form;

    public function delete(Form $form): bool;

    public function findById(string $id): ?Form;

    public function findByIdOrFail(string $id): Form;

    public function findByUserId(string $userId): Collection;

    public function update(Form $form, array $data): Form;

    public function userOwnsForm(string $userId, string $formId): bool;
}
