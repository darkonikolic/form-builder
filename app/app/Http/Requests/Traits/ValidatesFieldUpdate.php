<?php

declare(strict_types=1);

namespace App\Http\Requests\Traits;

trait ValidatesFieldUpdate
{
    /**
     * Get common validation rules for fields update.
     */
    protected function getCommonFieldRules(): array
    {
        return [
            'type' => ['sometimes', 'string', 'in:' . implode(',', config('form-builder.valid_field_types', []))],
            'configuration' => ['sometimes', 'array'],
            'configuration.name' => ['sometimes', 'string', 'max:255'],
            'configuration.label' => ['required', 'array'],
            'configuration.label.*' => ['required', 'string', 'max:255'],
            'configuration.required' => ['sometimes', 'boolean'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'configuration.placeholder' => ['nullable', 'string', 'max:255'],
            'configuration.options' => ['sometimes', 'array'],
            'configuration.options.*.value' => ['required_with:configuration.options', 'string', 'max:255'],
            'configuration.options.*.label' => ['required_with:configuration.options', 'array'],
            'configuration.options.*.label.*' => ['required_with:configuration.options.*.label', 'string', 'max:255'],
        ];
    }

    /**
     * Get validation rules for field labels based on form locales for update.
     */
    protected function getLabelValidationRules(array $formLocales): array
    {
        $rules = ['configuration.label' => ['required', 'array']];

        foreach ($formLocales as $locale) {
            $rules["configuration.label.{$locale}"] = ['required', 'string', 'max:255'];
        }

        return $rules;
    }
}
