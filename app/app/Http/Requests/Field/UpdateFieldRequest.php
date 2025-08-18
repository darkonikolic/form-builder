<?php

declare(strict_types=1);

namespace App\Http\Requests\Field;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateFieldRequest extends FormRequest
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
            'configuration.label.en.required' => 'The English label is required.',
            'configuration.label.de.required' => 'The German label is required.',
            'configuration.options.*.label.en.required' => 'The English label is required for all options.',
            'configuration.options.*.label.de.required' => 'The German label is required for all options.',
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
            'type' => 'sometimes|string|in:text,email,password,number,textarea,select,checkbox,radio,file,date,time,datetime-local,url,tel,search,color,range,hidden',
            'order' => 'sometimes|integer|min:0',
            'configuration' => 'sometimes|array',
            'configuration.name' => 'sometimes|string|max:255',
            'configuration.label' => 'sometimes|array',
            'configuration.label.en' => 'sometimes|string|max:255',
            'configuration.label.de' => 'sometimes|string|max:255',
            'configuration.required' => 'sometimes|boolean',
            'configuration.placeholder' => 'sometimes|array',
            'configuration.placeholder.en' => 'sometimes|string|max:255',
            'configuration.placeholder.de' => 'sometimes|string|max:255',
            'configuration.options' => 'sometimes|array',
            'configuration.options.*.value' => 'required_with:configuration.options|string',
            'configuration.options.*.label' => 'required_with:configuration.options|array',
            'configuration.options.*.label.en' => 'required_with:configuration.options.*.label|string',
            'configuration.options.*.label.de' => 'required_with:configuration.options.*.label|string',
            'validation_rules' => 'sometimes|nullable|array',
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
            // Additional validation: if label is provided, both en and de must be present
            if ($this->has('configuration.label') && is_array($this->input('configuration.label'))) {
                $label = $this->input('configuration.label');

                if (isset($label['en']) && !isset($label['de'])) {
                    $validator->errors()->add('configuration.label.de', 'The German label is required when English label is present.');
                }

                if (isset($label['de']) && !isset($label['en'])) {
                    $validator->errors()->add('configuration.label.en', 'The English label is required when German label is present.');
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

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Automatically populate configuration.type from the base type field
        if ($this->has('type')) {
            $this->merge([
                'configuration' => array_merge($this->input('configuration', []), [
                    'type' => $this->input('type'),
                ]),
            ]);
        }
    }
}
