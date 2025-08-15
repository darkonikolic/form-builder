<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Field extends Model
{
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

        // 3. Validate validation rules
        $this->validateValidationRules($formLocales);
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

    private function validateValidationRules(array $requiredLocales): void
    {
        $rules = $this->validation_rules ?? [];

        if (empty($rules)) {
            return; // No rules = no validation
        }

        foreach ($rules as $ruleName => $rule) {
            // Check if rule has implementation
            if (!isset($rule['rule'])) {
                throw new ValidationException("Rule '{$ruleName}' must have rule implementation");
            }

            // Check if rule has error messages for all locales
            foreach ($requiredLocales as $locale) {
                if (!isset($rule['error_messages'][$locale])) {
                    throw new ValidationException("Rule '{$ruleName}' missing error message for locale: {$locale}");
                }
            }
        }
    }
}
