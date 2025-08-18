<?php

declare(strict_types=1);

namespace App\Http\Requests\Traits;

trait ValidatesFieldCreation
{
    /**
     * Get validation rules for creating fields.
     */
    protected function getStoreFieldRules(): array
    {
        return [
            'type' => ['required', 'string', 'in:' . implode(',', config('form-builder.valid_field_types', []))],
            'configuration' => ['required', 'array'],
            'configuration.name' => ['required', 'string', 'max:255'],
            'configuration.label' => ['required', 'array'],
            'configuration.label.*' => ['required', 'string', 'max:255'],
            'configuration.required' => ['boolean'],
            'order' => ['integer', 'min:0'],
            'configuration.placeholder' => ['nullable', 'string', 'max:255'],
            'configuration.options' => ['array'],
            'configuration.options.*.value' => ['required_with:configuration.options', 'string', 'max:255'],
            'configuration.options.*.label' => ['required_with:configuration.options', 'array'],
            'configuration.options.*.label.*' => ['required_with:configuration.options.*.label', 'string', 'max:255'],
        ];
    }

    /**
     * Get validation rules for field labels based on form locales.
     */
    protected function getStoreLabelValidationRules(array $formLocales): array
    {
        $rules = ['configuration.label' => ['required', 'array']];

        foreach ($formLocales as $locale) {
            $rules["configuration.label.{$locale}"] = ['required', 'string', 'max:255'];
        }

        return $rules;
    }
}
