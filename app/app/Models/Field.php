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
            $model->validateFieldData();
        });
    }

    private function validateAllowedAttributes(array $config, array $allowedAttributes, string $typeName): void
    {
        $configKeys = array_keys($config);

        foreach ($configKeys as $key) {
            if (!in_array($key, $allowedAttributes) &&
                !in_array($key, ['type', 'name', 'label', 'required', 'class', 'style', 'placeholder'])) {
                throw new \Exception("Attribute '{$key}' is not allowed for {$typeName} fields. Allowed: " . implode(', ', $allowedAttributes));
            }
        }
    }

    private function validateBasicConfiguration(): void
    {
        $config = $this->configuration ?? [];

        $rules = [
            'type' => 'required|string|in:text,email,password,number,textarea,select,checkbox,radio,file,date,time,datetime-local,url,tel,search,color,range,hidden',
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

    private function validateCheckboxTypeFields(array $config): void
    {
        $allowedAttributes = [
            'checked', 'value', 'disabled', 'autofocus', 'required',
        ];

        $this->validateAllowedAttributes($config, $allowedAttributes, 'checkbox-type');
    }

    private function validateColorTypeFields(array $config): void
    {
        $allowedAttributes = [
            'disabled', 'autofocus', 'required',
        ];

        $this->validateAllowedAttributes($config, $allowedAttributes, 'color-type');
    }

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
                throw new \Exception('Invalid date format for min/max values');
            }

            if ($minDate >= $maxDate) {
                throw new \Exception('min date must be before max date');
            }
        }

        // Step validation for time fields
        if (isset($config['step']) && in_array($config['type'], ['time', 'datetime-local'])) {
            if (!preg_match('/^\d+$/', $config['step'])) {
                throw new \Exception('Step must be a positive integer for time fields');
            }
        }
    }

    private function validateFieldData(): void
    {
        $form = $this->form;
        if (!$form) {
            throw new ValidationException('Field must belong to a form');
        }

        $formLocales = $form->configuration['locales'] ?? [];

        if (empty($formLocales)) {
            throw new ValidationException('Form must have locales defined');
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

    private function validateFieldTypeSpecific(): void
    {
        $config = $this->configuration ?? [];
        $type = $config['type'] ?? '';

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
                    throw new \Exception("Invalid accept type format: {$type}");
                }
            }
        }
    }

    private function validateHiddenTypeFields(array $config): void
    {
        $allowedAttributes = [
            'value', 'disabled',
        ];

        $this->validateAllowedAttributes($config, $allowedAttributes, 'hidden-type');
    }

    private function validateI18nData(array $requiredLocales): void
    {
        $config = $this->configuration ?? [];

        foreach ($requiredLocales as $locale) {
            if (!isset($config['label'][$locale]) || empty($config['label'][$locale])) {
                throw new ValidationException("Missing label for locale: {$locale}");
            }
        }

        // Validate that no extra locales exist in label beyond required ones
        if (isset($config['label'])) {
            $labelLocales = array_keys($config['label']);
            foreach ($labelLocales as $locale) {
                if (!in_array($locale, $requiredLocales)) {
                    throw new ValidationException("Label has unregistered locale: {$locale}. Only locales registered in form are allowed.");
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
                            throw new ValidationException("Option {$index} label has unregistered locale: {$locale}. Only locales registered in form are allowed.");
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
                            throw new ValidationException("Validation message for rule '{$ruleName}' has unregistered locale: {$locale}. Only locales registered in form are allowed.");
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
                            throw new ValidationException("UI element '{$element}' has unregistered locale: {$locale}. Only locales registered in form are allowed.");
                        }
                    }
                }
            }
        }
    }

    private function validateNumberTypeFields(array $config): void
    {
        $allowedAttributes = [
            'min', 'max', 'step', 'readonly', 'disabled', 'placeholder',
        ];

        $this->validateAllowedAttributes($config, $allowedAttributes, 'number-type');

        // Cross-field validation
        if (isset($config['min']) && isset($config['max'])) {
            if ($config['min'] >= $config['max']) {
                throw new \Exception('min must be less than max');
            }
        }

        if (isset($config['step']) && isset($config['min']) && isset($config['max'])) {
            $range = $config['max'] - $config['min'];
            if ($config['step'] > $range) {
                throw new \Exception('step cannot be greater than the range between min and max');
            }
        }
    }

    private function validateRequiredFields(array $config): void
    {
        // Required fields for ALL field types
        $requiredFields = ['name'];

        foreach ($requiredFields as $field) {
            if (!isset($config[$field]) || empty($config[$field])) {
                throw new \Exception("Field '{$field}' is required for all field types");
            }
        }
    }

    private function validateSelectTypeFields(array $config): void
    {
        $allowedAttributes = [
            'multiple', 'size', 'required', 'disabled', 'autofocus', 'placeholder', 'options', 'inline', 'validation', 'ui',
        ];

        $this->validateAllowedAttributes($config, $allowedAttributes, 'select-type');

        // Options validation (required for select/radio)
        if (!isset($config['options']) || !is_array($config['options']) || empty($config['options'])) {
            throw new \Exception('Options array is required for select/radio fields');
        }

        foreach ($config['options'] as $index => $option) {
            if (!isset($option['value']) || !isset($option['label'])) {
                throw new \Exception("Option {$index} must have both 'value' and 'label'");
            }
        }

        // Size validation for multiple select
        if (isset($config['multiple']) && $config['multiple'] && isset($config['size'])) {
            if ($config['size'] < 2) {
                throw new \Exception('Size must be at least 2 for multiple select fields');
            }
        }
    }

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
                throw new \Exception('minlength cannot be greater than maxlength');
            }
        }
    }

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
                throw new \Exception('minlength cannot be greater than maxlength');
            }
        }
    }

    private function validateValidationRules(array $requiredLocales): void
    {
        $rules = $this->validation_rules ?? [];

        if (empty($rules)) {
            return; // No rules = no validation
        }

        foreach ($rules as $ruleName => $rule) {
            // Check if rule has implementation
            if (!isset($rule['error_messages'])) {
                throw new \Exception("Rule '{$ruleName}' must have error_messages");
            }

            // Check if rule has error messages for all locales
            foreach ($requiredLocales as $locale) {
                if (!isset($rule['error_messages'][$locale])) {
                    throw new \Exception("Rule '{$ruleName}' missing error message for locale: {$locale}");
                }
            }
        }
    }
}
