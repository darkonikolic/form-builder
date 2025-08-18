<?php

declare(strict_types=1);

namespace App\Http\Requests\Field;

use App\Http\Requests\Traits\ValidatesFieldCreation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreFieldRequest extends FormRequest
{
    use ValidatesFieldCreation;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.in' => 'The selected field type is invalid.',
            'configuration.label.required' => 'Field labels are required for all form locales.',
            'configuration.label.*.required' => 'Field label is required for this locale.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $form = $this->route('form');
        $formLocales = $form->configuration['locales'] ?? ['en', 'de'];

        return array_merge(
            $this->getStoreFieldRules(),
            $this->getStoreLabelValidationRules($formLocales),
        );
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
