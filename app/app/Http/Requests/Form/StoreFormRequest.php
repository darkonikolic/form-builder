<?php

declare(strict_types=1);

namespace App\Http\Requests\Form;

use App\Http\Requests\Traits\ValidatesFormCreation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreFormRequest extends FormRequest
{
    use ValidatesFormCreation;

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
        return array_merge(
            $this->getStoreFormRules(),
            $this->getStoreFormValidationRules(),
        );
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
        $this->configureFormCreationValidator($validator);
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
