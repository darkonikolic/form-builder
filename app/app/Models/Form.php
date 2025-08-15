<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Form extends Model
{
    use HasUuids;

    protected $casts = [
        'is_active' => 'boolean',
        'name' => 'array',
        'description' => 'array',
        'configuration' => 'array',
    ];

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'configuration',
    ];

    // Relationship with Field model
    public function fields(): HasMany
    {
        return $this->hasMany(Field::class)->orderBy('order');
    }

    // Validation before saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model): void {
            $model->validateFormConfiguration();
        });
    }

    private function validateFormConfiguration(): void
    {
        $config = $this->configuration ?? [];
        $locales = $config['locales'] ?? [];

        // Validate if locales are valid
        $validLocales = ['en', 'de', 'it', 'fr'];
        foreach ($locales as $locale) {
            if (!in_array($locale, $validLocales)) {
                throw new ValidationException("Invalid locale: {$locale}");
            }
        }

        // Validate if name and description are present for all locales
        foreach ($locales as $locale) {
            if (!isset($this->name[$locale]) || empty($this->name[$locale])) {
                throw new ValidationException("Missing name for locale: {$locale}");
            }
            if (!isset($this->description[$locale]) || empty($this->description[$locale])) {
                throw new ValidationException("Missing description for locale: {$locale}");
            }
        }

        // Validate that no extra locales exist in name/description beyond registered ones
        $nameLocales = array_keys($this->name ?? []);
        $descriptionLocales = array_keys($this->description ?? []);

        foreach ($nameLocales as $locale) {
            if (!in_array($locale, $locales)) {
                throw new ValidationException("Name has unregistered locale: {$locale}. Only registered locales are allowed.");
            }
        }

        foreach ($descriptionLocales as $locale) {
            if (!in_array($locale, $locales)) {
                throw new ValidationException("Description has unregistered locale: {$locale}. Only registered locales are allowed.");
            }
        }
    }
}
