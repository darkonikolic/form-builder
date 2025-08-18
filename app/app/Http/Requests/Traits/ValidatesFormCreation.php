<?php

declare(strict_types=1);

namespace App\Http\Requests\Traits;

use Illuminate\Contracts\Validation\Validator;

trait ValidatesFormCreation
{
    /**
     * Configure the validator instance for form creation.
     */
    protected function configureFormCreationValidator(Validator $validator): void
    {
        $validator->after(function ($validator): void {
            // Validate that all required locales have values
            if ($this->has('configuration.locales') && $this->has('name')) {
                $locales = $this->input('configuration.locales');
                foreach ($locales as $locale) {
                    if (!isset($this->input('name')[$locale]) || empty($this->input('name')[$locale])) {
                        $validator->errors()->add("name.{$locale}", "Name for locale '{$locale}' is required");
                    }
                }
            }

            // Validate that all name keys are within allowed locales
            if ($this->has('configuration.locales') && $this->has('name')) {
                $allowedLocales = $this->input('configuration.locales');
                foreach (array_keys($this->input('name')) as $locale) {
                    if (!in_array($locale, $allowedLocales)) {
                        $validator->errors()->add("name.{$locale}", "Locale '{$locale}' is not in allowed locales: " . implode(', ', $allowedLocales));
                    }
                }
            }

            // Validate that all description keys are within allowed locales (if description exists)
            if ($this->has('configuration.locales') && $this->has('description')) {
                $allowedLocales = $this->input('configuration.locales');
                foreach (array_keys($this->input('description')) as $locale) {
                    if (!in_array($locale, $allowedLocales)) {
                        $validator->errors()->add("description.{$locale}", "Locale '{$locale}' is not in allowed locales: " . implode(', ', $allowedLocales));
                    }
                }
            }
        });
    }

    /**
     * Get validation rules for creating forms.
     */
    protected function getStoreFormRules(): array
    {
        return [
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
            'configuration' => 'required|array',
            'configuration.locales' => 'required|array|min:1',
        ];
    }

    /**
     * Get validation rules for form locales.
     */
    protected function getStoreFormValidationRules(): array
    {
        $validLocales = implode(',', config('form-builder.valid_locales', ['en', 'de']));

        return [
            'configuration.locales.*' => "string|in:{$validLocales}",
        ];
    }
}
