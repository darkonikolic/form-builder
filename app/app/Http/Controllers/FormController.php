<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Forms",
 *     description="Form management endpoints"
 * )
 */
class FormController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Remove the specified form.
     *
     * @OA\Delete(
     *     path="/api/forms/{id}",
     *     operationId="deleteForm",
     *     tags={"Forms"},
     *     summary="Delete a specific form",
     *     description="Delete a specific form by ID for the authenticated user. All associated fields will be deleted automatically.",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Form ID",
     *         required=true,
     *
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Form deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Form deleted successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Form not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Form not found")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to delete form"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $form = Auth::user()->forms()->findOrFail($id);

            // Delete form (fields will be deleted automatically due to cascade)
            $form->delete();

            return response()->json([
                'success' => true,
                'message' => 'Form deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Form not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete form',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a listing of the user's forms.
     *
     * @OA\Get(
     *     path="/api/forms",
     *     operationId="getForms",
     *     tags={"Forms"},
     *     summary="Get all forms for authenticated user",
     *     description="Retrieve a list of all forms belonging to the authenticated user",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Forms retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Form")),
     *             @OA\Property(property="message", type="string", example="Forms retrieved successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve forms"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $forms = Auth::user()->forms()->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $forms,
                'message' => 'Forms retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve forms',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified form.
     *
     * @OA\Get(
     *     path="/api/forms/{id}",
     *     operationId="getForm",
     *     tags={"Forms"},
     *     summary="Get a specific form",
     *     description="Retrieve a specific form by ID for the authenticated user",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Form ID",
     *         required=true,
     *
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Form retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Form"),
     *             @OA\Property(property="message", type="string", example="Form retrieved successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Form not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Form not found")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve form"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $form = Auth::user()->forms()->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $form->load('fields'),
                'message' => 'Form retrieved successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Form not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve form',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created form.
     *
     * @OA\Post(
     *     path="/api/forms",
     *     operationId="createForm",
     *     tags={"Forms"},
     *     summary="Create a new form",
     *     description="Create a new form for the authenticated user",
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name","configuration"},
     *
     *             @OA\Property(
     *                 property="name",
     *                 type="object",
     *                 required={"en","de"},
     *                 @OA\Property(property="en", type="string", maxLength=255, example="Contact Form"),
     *                 @OA\Property(property="de", type="string", maxLength=255, example="Kontaktformular")
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="object",
     *                 @OA\Property(property="en", type="string", maxLength=1000, example="A contact form for customer inquiries"),
     *                 @OA\Property(property="de", type="string", maxLength=1000, example="Ein Kontaktformular für Kundenanfragen")
     *             ),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(
     *                 property="configuration",
     *                 type="object",
     *                 required={"locales"},
     *                 @OA\Property(
     *                     property="locales",
     *                     type="array",
     *
     *                     @OA\Items(type="string", enum={"en","de","it","fr"}),
     *                     example={"en","de"}
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Form created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Form"),
     *             @OA\Property(property="message", type="string", example="Form created successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to create form"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|array',
                'name.*' => 'required|string|max:255',
                'description' => 'nullable|array',
                'description.*' => 'nullable|string|max:1000',
                'is_active' => 'boolean',
                'configuration' => 'required|array',
                'configuration.locales' => 'required|array|min:1',
                'configuration.locales.*' => 'string|in:en,de,it,fr',
            ]);

            // Validate that all required locales have values
            foreach ($validated['configuration']['locales'] as $locale) {
                if (!isset($validated['name'][$locale]) || empty($validated['name'][$locale])) {
                    throw ValidationException::withMessages([
                        "name.{$locale}" => ["Name for locale '{$locale}' is required"],
                    ]);
                }
                if (isset($validated['description']) && (!isset($validated['description'][$locale]) || empty($validated['description'][$locale]))) {
                    throw ValidationException::withMessages([
                        "description.{$locale}" => ["Description for locale '{$locale}' is required"],
                    ]);
                }
            }

            // Validate that all name keys are within allowed locales
            $allowedLocales = $validated['configuration']['locales'];
            foreach (array_keys($validated['name']) as $locale) {
                if (!in_array($locale, $allowedLocales)) {
                    throw ValidationException::withMessages([
                        "name.{$locale}" => ["Locale '{$locale}' is not in allowed locales: " . implode(', ', $allowedLocales)],
                    ]);
                }
            }

            // Validate that all description keys are within allowed locales (if description exists)
            if (isset($validated['description'])) {
                foreach (array_keys($validated['description']) as $locale) {
                    if (!in_array($locale, $allowedLocales)) {
                        throw ValidationException::withMessages([
                            "description.{$locale}" => ["Locale '{$locale}' is not in allowed locales: " . implode(', ', $allowedLocales)],
                        ]);
                    }
                }
            }

            $form = Auth::user()->forms()->create($validated);

            return response()->json([
                'success' => true,
                'data' => $form,
                'message' => 'Form created successfully',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create form',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified form.
     *
     * @OA\Put(
     *     path="/api/forms/{id}",
     *     operationId="updateForm",
     *     tags={"Forms"},
     *     summary="Update a specific form",
     *     description="Update a specific form by ID for the authenticated user",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Form ID",
     *         required=true,
     *
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="name",
     *                 type="object",
     *                 @OA\Property(property="en", type="string", maxLength=255, example="Updated Contact Form"),
     *                 @OA\Property(property="de", type="string", maxLength=255, example="Aktualisiertes Kontaktformular")
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="object",
     *                 @OA\Property(property="en", type="string", maxLength=1000, example="Updated contact form description"),
     *                 @OA\Property(property="de", type="string", maxLength=1000, example="Aktualisierte Kontaktformular-Beschreibung")
     *             ),
     *             @OA\Property(property="is_active", type="boolean", example=false),
     *             @OA\Property(
     *                 property="configuration",
     *                 type="object",
     *                 @OA\Property(
     *                     property="locales",
     *                     type="array",
     *
     *                     @OA\Items(type="string", enum={"en","de","it","fr"}),
     *                     example={"en","de","it"}
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Form updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Form"),
     *             @OA\Property(property="message", type="string", example="Form updated successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Form not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Form not found")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to update form"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $form = Auth::user()->forms()->findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|array',
                'name.*' => 'sometimes|string|max:255',
                'description' => 'sometimes|nullable|array',
                'description.*' => 'sometimes|nullable|string|max:1000',
                'is_active' => 'sometimes|boolean',
                'configuration' => 'sometimes|array',
                'configuration.locales' => 'sometimes|array|min:1',
                'configuration.locales.*' => 'string|in:en,de,it,fr',
            ]);

            // Additional validation: if name is provided, ensure all locales have values
            if (isset($validated['name']) && is_array($validated['name'])) {
                $nameLocales = array_keys($validated['name']);
                foreach ($nameLocales as $locale) {
                    if (empty($validated['name'][$locale])) {
                        throw ValidationException::withMessages([
                            "name.{$locale}" => ["The name.{$locale} field cannot be empty."],
                        ]);
                    }
                }
            }

            // Additional validation: if configuration.locales is provided, ensure all locales have name values
            if (isset($validated['configuration']['locales']) && isset($validated['name'])) {
                foreach ($validated['configuration']['locales'] as $locale) {
                    if (!isset($validated['name'][$locale]) || empty($validated['name'][$locale])) {
                        throw ValidationException::withMessages([
                            "name.{$locale}" => ["Name for locale '{$locale}' is required when updating configuration.locales"],
                        ]);
                    }
                }
            }

            $form->update($validated);

            return response()->json([
                'success' => true,
                'data' => $form->fresh(),
                'message' => 'Form updated successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Form not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update form',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
