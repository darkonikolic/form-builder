<?php

declare(strict_types=1);

namespace App\Http\Requests\Form;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'configuration.locales.required' => 'The configuration.locales field is required.',
            'configuration.locales.min' => 'At least one locale must be specified.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
            'configuration' => 'required|array',
            'configuration.locales' => 'required|array|min:1',
            'configuration.locales.*' => 'string|in:en,de,it,fr',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  Validator  $validator
     *
     * @return void
     */
    public function withValidator($validator): void
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
     * Return JSON structure expected by tests on validation failure.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
