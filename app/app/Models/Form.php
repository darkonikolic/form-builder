<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Schema(
 *     schema="Form",
 *     title="Form",
 *     description="Form model",
 *
 *     @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *     @OA\Property(property="user_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440001"),
 *     @OA\Property(
 *         property="name",
 *         type="object",
 *         description="Form name in multiple languages",
 *         @OA\Property(property="en", type="string", example="Contact Form"),
 *         @OA\Property(property="de", type="string", example="Kontaktformular")
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="object",
 *         description="Form description in multiple languages",
 *         @OA\Property(property="en", type="string", example="A contact form for customer inquiries"),
 *         @OA\Property(property="de", type="string", example="Ein Kontaktformular für Kundenanfragen")
 *     ),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(
 *         property="configuration",
 *         type="object",
 *         description="Form configuration including locales",
 *         @OA\Property(
 *             property="locales",
 *             type="array",
 *
 *             @OA\Items(type="string", enum={"en","de","it","fr"}),
 *             example={"en","de"}
 *         )
 *     ),
 *
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="fields",
 *         type="array",
 *         description="Form fields",
 *
 *         @OA\Items(ref="#/components/schemas/Field")
 *     )
 * )
 */
class Form extends Model
{
    use HasFactory;
    use HasUuids;

    protected $casts = [
        'is_active' => 'boolean',
        'name' => 'array',
        'description' => 'array',
        'configuration' => 'array',
    ];

    protected $fillable = [
        'user_id',
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

    // Relationship with User model
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Validation before saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model): void {
            $model->validateFormConfiguration();
        });
    }

    /**
     * @throws ValidationException
     */
    private function validateFormConfiguration(): void
    {
        $config = $this->configuration ?? [];
        $locales = $config['locales'] ?? [];
        $validLocales = config('form-builder.valid_locales', ['en', 'de']);

        // Validate if locales are valid
        foreach ($locales as $locale) {
            if (!in_array($locale, $validLocales, true)) {
                throw ValidationException::withMessages([
                    "configuration.locales.{$locale}" => ["Invalid locale: {$locale}. Allowed: " . implode(', ', $validLocales)],
                ]);
            }
        }

        // Validate if name is present for all locales
        foreach ($locales as $locale) {
            if (!isset($this->name[$locale]) || empty($this->name[$locale])) {
                throw ValidationException::withMessages([
                    "name.{$locale}" => ["Missing name for locale: {$locale}"],
                ]);
            }
        }

        // Validate that no extra locales exist in name beyond registered ones
        $nameLocales = array_keys($this->name ?? []);
        foreach ($nameLocales as $locale) {
            if (!in_array($locale, $locales)) {
                throw ValidationException::withMessages([
                    "name.{$locale}" => ["Name has unregistered locale: {$locale}. Only registered locales are allowed."],
                ]);
            }
        }

        // Validate description if provided (optional)
        if ($this->description) {
            $descriptionLocales = array_keys($this->description);
            foreach ($descriptionLocales as $locale) {
                if (!in_array($locale, $locales)) {
                    throw ValidationException::withMessages([
                        "description.{$locale}" => ["Description has unregistered locale: {$locale}. Only locales registered in form are allowed."],
                    ]);
                }
            }
        }
    }
}
