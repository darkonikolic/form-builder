<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Schema(
 *     schema="Field",
 *     title="Field",
 *     description="Form field model",
 *
 *     @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440002"),
 *     @OA\Property(property="form_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(
 *         property="configuration",
 *         type="object",
 *         description="Field configuration including type, name, label, and HTML attributes",
 *         @OA\Property(property="type", type="string", enum={"text","email","password","number","textarea","select","checkbox","radio","file","date","time","datetime-local","url","tel","search","color","range","hidden"}, example="text"),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             description="Field name attribute",
 *             example="first_name"
 *         ),
 *         @OA\Property(
 *             property="label",
 *             type="object",
 *             description="Field label in multiple languages",
 *             @OA\Property(property="en", type="string", example="First Name"),
 *             @OA\Property(property="de", type="string", example="Vorname")
 *         ),
 *         @OA\Property(property="required", type="boolean", example=true),
 *         @OA\Property(property="class", type="string", example="form-control"),
 *         @OA\Property(property="style", type="string", example="width: 100%"),
 *         @OA\Property(
 *             property="placeholder",
 *             type="object",
 *             description="Placeholder text in multiple languages",
 *             @OA\Property(property="en", type="string", example="Enter your first name"),
 *             @OA\Property(property="de", type="string", example="Geben Sie Ihren Vornamen ein")
 *         )
 *     ),
 *     @OA\Property(
 *         property="validation_rules",
 *         type="object",
 *         description="Field validation rules",
 *         nullable=true
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Field extends Model
{
    use HasFactory;
    use HasUuids;

    protected $casts = [
        'order' => 'integer',
        'configuration' => 'array',
        'validation_rules' => 'array',
    ];

    protected $fillable = [
        'form_id',
        'type',
        'order',
        'configuration',
        'validation_rules',
    ];

    // Relationship with Form model
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    // Validation before saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model): void {
            $model->syncTypeFields();
            $model->validateFieldData();
        });
    }

    /**
     * Automatically sync type column with configuration.type.
     *
     * @throws ValidationException
     */
    private function syncTypeFields(): void
    {
        $config = $this->configuration ?? [];
        $validTypes = config('form-builder.valid_field_types', ['text', 'email', 'password', 'number', 'textarea', 'select', 'checkbox', 'radio']);

        // Validate field type if it's set
        if (isset($this->type)) {
            if (!in_array($this->type, $validTypes)) {
                throw ValidationException::withMessages([
                    'type' => ['Invalid field type: ' . $this->type . '. Valid types are: ' . implode(', ', $validTypes)],
                ]);
            }
        }

        // If configuration.type is set, sync it to the type column
        if (isset($config['type']) && $config['type'] !== $this->type) {
            $this->type = $config['type'];
        }

        // If type column is set but configuration.type is not, sync it to configuration
        if (isset($this->type) && (!isset($config['type']) || $config['type'] !== $this->type)) {
            $config['type'] = $this->type;
            $this->configuration = $config;
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateAllowedAttributes(array $config, array $allowedAttributes, string $typeName): void
    {
        $configKeys = array_keys($config);

        foreach ($configKeys as $key) {
            if (!in_array($key, $allowedAttributes) &&
                !in_array($key, ['type', 'name', 'label', 'required', 'class', 'style', 'placeholder'])) {
                throw ValidationException::withMessages([
                    "configuration.{$key}" => ["Attribute '{$key}' is not allowed for {$typeName} fields. Allowed: " . implode(', ', $allowedAttributes)],
                ]);
            }
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateBasicConfiguration(): void
    {
        $config = $this->configuration ?? [];

        $rules = [
            'required' => 'boolean',
            'default_value' => 'nullable|string|max:1000',
            'class' => 'nullable|string|max:500',
            'style' => 'nullable|string|max:1000',
        ];

        $validator = Validator::make($config, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateCheckboxTypeFields(array $config): void
    {
        $allowedAttributes = [
            'checked', 'value', 'disabled', 'autofocus', 'required',
        ];

        $this->validateAllowedAttributes($config, $allowedAttributes, 'checkbox-type');
    }

    /**
     * @throws ValidationException
     */
    private function validateColorTypeFields(array $config): void
    {
        $allowedAttributes = [
            'disabled', 'autofocus', 'required',
        ];

        $this->validateAllowedAttributes($config, $allowedAttributes, 'color-type');
    }

    /**
     * @throws ValidationException
     */
    private function validateDateTimeTypeFields(array $config): void
    {
        $allowedAttributes = [
            'min', 'max', 'step', 'readonly', 'disabled', 'autofocus', 'required',
        ];

        $this->validateAllowedAttributes($config, $allowedAttributes, 'datetime-type');

        // Date validation
        if (isset($config['min']) && isset($config['max'])) {
            $minDate = strtotime($config['min']);
            $maxDate = strtotime($config['max']);

            if ($minDate === false || $maxDate === false) {
                throw ValidationException::withMessages([
                    'configuration.min' => ['Invalid date format for min value'],
                    'configuration.max' => ['Invalid date format for max value'],
                ]);
            }

            if ($minDate >= $maxDate) {
                throw ValidationException::withMessages([
                    'configuration.min' => ['Min date must be before max date'],
                    'configuration.max' => ['Max date must be after min date'],
                ]);
            }
        }

        // Step validation for time fields
        if (isset($config['step']) && in_array($this->type, ['time', 'datetime-local'])) {
            if (!preg_match('/^\d+$/', $config['step'])) {
                throw ValidationException::withMessages([
                    'configuration.step' => ['Step must be a positive integer for time fields'],
                ]);
            }
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateFieldData(): void
    {
        $form = $this->form;
        if (!$form) {
            throw ValidationException::withMessages([
                'form_id' => ['Field must belong to a form'],
            ]);
        }

        $formLocales = $form->configuration['locales'] ?? [];

        if (empty($formLocales)) {
            throw ValidationException::withMessages([
                'configuration.locales' => ['Form must have locales defined'],
            ]);
        }

        // 1. Validate basic configuration
        $this->validateBasicConfiguration();

        // 2. Validate i18n data
        $this->validateI18nData($formLocales);

        // 3. Validate type-specific configuration
        $this->validateFieldTypeSpecific();

        // 4. Validate validation rules
        $this->validateValidationRules($formLocales);
    }

    /**
     * @throws ValidationException
     */
    private function validateFieldTypeSpecific(): void
    {
        $config = $this->configuration ?? [];
        $type = $this->type ?? '';

        // Validate required fields for all types
        $this->validateRequiredFields($config);

        // Validate type-specific HTML attributes
        switch ($type) {
            case 'text':
            case 'email':
            case 'password':
            case 'tel':
            case 'search':
            case 'url':
                $this->validateTextTypeFields($config);

                break;
            case 'number':
            case 'range':
                $this->validateNumberTypeFields($config);

                break;
            case 'select':
            case 'radio':
                $this->validateSelectTypeFields($config);

                break;
            case 'checkbox':
                $this->validateCheckboxTypeFields($config);

                break;
            case 'textarea':
                $this->validateTextareaTypeFields($config);

                break;
            case 'file':
                $this->validateFileTypeFields($config);

                break;
            case 'date':
            case 'time':
            case 'datetime-local':
                $this->validateDateTimeTypeFields($config);

                break;
            case 'color':
                $this->validateColorTypeFields($config);

                break;
            case 'hidden':
                $this->validateHiddenTypeFields($config);

                break;
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateFileTypeFields(array $config): void
    {
        $allowedAttributes = [
            'accept', 'multiple', 'capture', 'disabled', 'autofocus', 'required',
        ];

        $this->validateAllowedAttributes($config, $allowedAttributes, 'file-type');

        // Accept validation (basic MIME type check)
        if (isset($config['accept'])) {
            $acceptTypes = explode(',', $config['accept']);
            foreach ($acceptTypes as $type) {
                $type = trim($type);
                if (!preg_match('/^(\*\/\*|[a-z]+\/[a-z0-9.-]+|[a-z]+\.[a-z0-9.-]+)$/i', $type)) {
                    throw ValidationException::withMessages([
                        'configuration.accept' => ["Invalid accept type format: {$type}"],
                    ]);
                }
            }
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateHiddenTypeFields(array $config): void
    {
        $allowedAttributes = [
            'value', 'disabled',
        ];

        $this->validateAllowedAttributes($config, $allowedAttributes, 'hidden-type');
    }

    /**
     * @throws ValidationException
     */
    private function validateI18nData(array $requiredLocales): void
    {
        $config = $this->configuration ?? [];

        foreach ($requiredLocales as $locale) {
            if (!isset($config['label'][$locale]) || empty($config['label'][$locale])) {
                throw ValidationException::withMessages([
                    "configuration.label.{$locale}" => ["Missing label for locale: {$locale}"],
                ]);
            }
        }

        // Validate that no extra locales exist in label beyond required ones
        if (isset($config['label'])) {
            $labelLocales = array_keys($config['label']);
            foreach ($labelLocales as $locale) {
                if (!in_array($locale, $requiredLocales)) {
                    throw ValidationException::withMessages([
                        "configuration.label.{$locale}" => [
                            "Label has unregistered locale: {$locale}. Only locales registered in form are allowed.",
                        ],
                    ]);
                }
            }
        }

        // Validate options i18n if they exist
        if (isset($config['options']) && is_array($config['options'])) {
            foreach ($config['options'] as $index => $option) {
                if (isset($option['label']) && is_array($option['label'])) {
                    $optionLabelLocales = array_keys($option['label']);
                    foreach ($optionLabelLocales as $locale) {
                        if (!in_array($locale, $requiredLocales)) {
                            throw ValidationException::withMessages([
                                "configuration.options.{$index}.label.{$locale}" => [
                                    "Option {$index} label has unregistered locale: {$locale}. Only locales registered in form are allowed.",
                                ],
                            ]);
                        }
                    }
                }
            }
        }

        // Validate validation messages i18n if they exist
        if (isset($config['validation']['messages']) && is_array($config['validation']['messages'])) {
            foreach ($config['validation']['messages'] as $ruleName => $messages) {
                if (is_array($messages)) {
                    $messageLocales = array_keys($messages);
                    foreach ($messageLocales as $locale) {
                        if (!in_array($locale, $requiredLocales)) {
                            throw ValidationException::withMessages([
                                "configuration.validation.messages.{$ruleName}.{$locale}" => [
                                    "Validation message for rule '{$ruleName}' has unregistered locale: {$locale}. Only locales registered in form are allowed.",
                                ],
                            ]);
                        }
                    }
                }
            }
        }

        // Validate UI elements i18n if they exist
        if (isset($config['ui']) && is_array($config['ui'])) {
            $uiElements = ['placeholder', 'title', 'aria-label', 'help-text'];
            foreach ($uiElements as $element) {
                if (isset($config['ui'][$element]) && is_array($config['ui'][$element])) {
                    $uiLocales = array_keys($config['ui'][$element]);
                    foreach ($uiLocales as $locale) {
                        if (!in_array($locale, $requiredLocales)) {
                            throw ValidationException::withMessages([
                                "configuration.ui.{$element}.{$locale}" => [
                                    "UI element '{$element}' has unregistered locale: {$locale}. Only locales registered in form are allowed.",
                                ],
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateNumberTypeFields(array $config): void
    {
        $allowedAttributes = [
            'min', 'max', 'step', 'readonly', 'disabled', 'placeholder',
        ];

        $this->validateAllowedAttributes($config, $allowedAttributes, 'number-type');

        // Cross-field validation
        if (isset($config['min']) && isset($config['max'])) {
            if ($config['min'] >= $config['max']) {
                throw ValidationException::withMessages([
                    'configuration.min' => ['Min value must be less than max value'],
                    'configuration.max' => ['Max value must be greater than min value'],
                ]);
            }
        }

        if (isset($config['step']) && isset($config['min']) && isset($config['max'])) {
            $range = $config['max'] - $config['min'];
            if ($config['step'] > $range) {
                throw ValidationException::withMessages([
                    'configuration.step' => ['Step cannot be greater than the range between min and max'],
                ]);
            }
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateRequiredFields(array $config): void
    {
        // Required fields for ALL field types
        $requiredFields = ['name'];

        foreach ($requiredFields as $field) {
            if (!isset($config[$field]) || empty($config[$field])) {
                throw ValidationException::withMessages([
                    "configuration.{$field}" => ["Field '{$field}' is required for all field types"],
                ]);
            }
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateSelectTypeFields(array $config): void
    {
        $allowedAttributes = [
            'multiple', 'size', 'required', 'disabled', 'autofocus', 'placeholder', 'options', 'inline', 'validation', 'ui',
        ];

        $this->validateAllowedAttributes($config, $allowedAttributes, 'select-type');

        // Options validation (required for select/radio)
        if (!isset($config['options']) || !is_array($config['options']) || empty($config['options'])) {
            throw ValidationException::withMessages([
                'configuration.options' => ['Options array is required for select/radio fields'],
            ]);
        }

        foreach ($config['options'] as $index => $option) {
            if (!isset($option['value']) || !isset($option['label'])) {
                throw ValidationException::withMessages([
                    "configuration.options.{$index}" => ["Option {$index} must have both 'value' and 'label'"],
                ]);
            }
        }

        // Size validation for multiple select
        if (isset($config['multiple']) && $config['multiple'] && isset($config['size'])) {
            if ($config['size'] < 2) {
                throw ValidationException::withMessages([
                    'configuration.size' => ['Size must be at least 2 for multiple select fields'],
                ]);
            }
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateTextareaTypeFields(array $config): void
    {
        $allowedAttributes = [
            'rows', 'cols', 'maxlength', 'minlength', 'readonly',
            'disabled', 'autofocus', 'spellcheck', 'wrap', 'placeholder',
        ];

        $this->validateAllowedAttributes($config, $allowedAttributes, 'textarea-type');

        // Cross-field validation
        if (isset($config['minlength']) && isset($config['maxlength'])) {
            if ($config['minlength'] > $config['maxlength']) {
                throw ValidationException::withMessages([
                    'configuration.minlength' => ['Minlength cannot be greater than maxlength'],
                    'configuration.maxlength' => ['Maxlength cannot be less than minlength'],
                ]);
            }
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateTextTypeFields(array $config): void
    {
        // HTML attributes for text-type inputs
        $allowedAttributes = [
            'maxlength', 'minlength', 'pattern', 'autocomplete', 'size',
            'readonly', 'disabled', 'autofocus', 'spellcheck', 'placeholder',
        ];

        $this->validateAllowedAttributes($config, $allowedAttributes, 'text-type');

        // Cross-field validation
        if (isset($config['minlength']) && isset($config['maxlength'])) {
            if ($config['minlength'] > $config['maxlength']) {
                throw ValidationException::withMessages([
                    'configuration.minlength' => ['Minlength cannot be greater than maxlength'],
                    'configuration.maxlength' => ['Maxlength cannot be less than minlength'],
                ]);
            }
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateValidationRules(array $requiredLocales): void
    {
        $rules = $this->validation_rules ?? [];

        if (empty($rules)) {
            return; // No rules = no validation
        }

        foreach ($rules as $ruleName => $rule) {
            // Check if rule has implementation
            if (!isset($rule['error_messages'])) {
                throw ValidationException::withMessages([
                    "validation_rules.{$ruleName}" => ["Rule '{$ruleName}' must have error_messages"],
                ]);
            }

            // Check if rule has error messages for all locales
            foreach ($requiredLocales as $locale) {
                if (!isset($rule['error_messages'][$locale])) {
                    throw ValidationException::withMessages([
                        "validation_rules.{$ruleName}.error_messages.{$locale}" => ["Rule '{$ruleName}' missing error message for locale: {$locale}"],
                    ]);
                }
            }
        }
    }
}
