<?php

declare(strict_types=1);

namespace App\Http\Requests\Traits;

use Illuminate\Contracts\Validation\Validator;

trait ValidatesFormUpdate
{
    /**
     * Configure the validator instance for form updates.
     */
    protected function configureFormUpdateValidator(Validator $validator): void
    {
        $validator->after(function ($validator): void {
            // Additional validation: if name is provided, ensure all locales have values
            if ($this->has('name') && is_array($this->input('name'))) {
                $nameLocales = array_keys($this->input('name'));
                foreach ($nameLocales as $locale) {
                    if (empty($this->input('name')[$locale])) {
                        $validator->errors()->add("name.{$locale}", "The name.{$locale} field cannot be empty.");
                    }
                }
            }

            // Additional validation: if configuration.locales is provided, ensure all locales have name values
            if ($this->has('configuration.locales') && $this->has('name')) {
                foreach ($this->input('configuration.locales') as $locale) {
                    if (!isset($this->input('name')[$locale]) || empty($this->input('name')[$locale])) {
                        $validator->errors()->add("name.{$locale}", "Name for locale '{$locale}' is required when updating configuration.locales");
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
     * Get common validation rules for forms update.
     */
    protected function getCommonFormRules(): array
    {
        return [
            'name' => 'sometimes|array',
            'name.*' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|array',
            'description.*' => 'sometimes|nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
            'configuration' => 'sometimes|array',
            'configuration.locales' => 'sometimes|array|min:1',
        ];
    }

    /**
     * Get validation rules for form locales for update.
     */
    protected function getFormValidationRules(): array
    {
        $validLocales = implode(',', config('form-builder.valid_locales', ['en', 'de']));

        return [
            'configuration.locales.*' => "string|in:{$validLocales}",
        ];
    }
}
