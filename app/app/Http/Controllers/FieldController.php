<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Form;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Fields",
 *     description="Form field management endpoints"
 * )
 */
class FieldController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Remove the specified field.
     *
     * @OA\Delete(
     *     path="/api/forms/{form_id}/fields/{field_id}",
     *     operationId="deleteField",
     *     tags={"Fields"},
     *     summary="Delete a specific field",
     *     description="Delete a specific field by ID for the authenticated user",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="form_id",
     *         in="path",
     *         description="Form ID",
     *         required=true,
     *
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *
     *     @OA\Parameter(
     *         name="field_id",
     *         in="path",
     *         description="Field ID",
     *         required=true,
     *
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Field deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Field deleted successfully")
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
     *         description="Form or field not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Form or field not found")
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
     *             @OA\Property(property="message", type="string", example="Failed to delete field"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(string $form_id, string $field_id): JsonResponse
    {
        try {
            $form = Auth::user()->forms()->findOrFail($form_id);
            $field = $form->fields()->findOrFail($field_id);

            $field->delete();

            return response()->json([
                'success' => true,
                'message' => 'Field deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Form or field not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete field',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a listing of fields for a specific form.
     *
     * @OA\Get(
     *     path="/api/forms/{form_id}/fields",
     *     operationId="getFields",
     *     tags={"Fields"},
     *     summary="Get all fields for a specific form",
     *     description="Retrieve a list of all fields belonging to a specific form for the authenticated user",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="form_id",
     *         in="path",
     *         description="Form ID",
     *         required=true,
     *
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Fields retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Field")),
     *             @OA\Property(property="message", type="string", example="Fields retrieved successfully")
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
     *             @OA\Property(property="message", type="string", example="Failed to retrieve fields"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function index(string $form_id): JsonResponse
    {
        try {
            $form = Auth::user()->forms()->findOrFail($form_id);
            $fields = $form->fields()->orderBy('order')->get();

            return response()->json([
                'success' => true,
                'data' => $fields,
                'message' => 'Fields retrieved successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Form not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve fields',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified field.
     *
     * @OA\Get(
     *     path="/api/forms/{form_id}/fields/{field_id}",
     *     operationId="getField",
     *     tags={"Fields"},
     *     summary="Get a specific field",
     *     description="Retrieve a specific field by ID for the authenticated user",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="form_id",
     *         in="path",
     *         description="Form ID",
     *         required=true,
     *
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *
     *     @OA\Parameter(
     *         name="field_id",
     *         in="path",
     *         description="Field ID",
     *         required=true,
     *
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Field retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Field"),
     *             @OA\Property(property="message", type="string", example="Field retrieved successfully")
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
     *         description="Form or field not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Form or field not found")
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
     *             @OA\Property(property="message", type="string", example="Failed to retrieve field"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function show(string $form_id, string $field_id): JsonResponse
    {
        try {
            $form = Auth::user()->forms()->findOrFail($form_id);
            $field = $form->fields()->findOrFail($field_id);

            return response()->json([
                'success' => true,
                'data' => $field,
                'message' => 'Field retrieved successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Form or field not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve field',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created field.
     *
     * @OA\Post(
     *     path="/api/forms/{form_id}/fields",
     *     operationId="createField",
     *     tags={"Fields"},
     *     summary="Create a new field for a specific form",
     *     description="Create a new field for a specific form for the authenticated user",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="form_id",
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
     *             required={"type","configuration"},
     *
     *             @OA\Property(property="type", type="string", enum={"text","email","password","number","textarea","select","checkbox","radio","file","date","time","datetime-local","url","tel","search","color","range","hidden"}, example="text"),
     *             @OA\Property(property="order", type="integer", example=1),
     *             @OA\Property(
     *                 property="configuration",
     *                 type="object",
     *                 required={"name","label"},
     *                 @OA\Property(property="name", type="string", example="first_name"),
     *                 @OA\Property(
     *                     property="label",
     *                     type="object",
     *                     required={"en","de"},
     *                     @OA\Property(property="en", type="string", example="First Name"),
     *                     @OA\Property(property="de", type="string", example="Vorname")
     *                 ),
     *                 @OA\Property(property="required", type="boolean", example=true),
     *                 @OA\Property(property="placeholder", type="object", @OA\Property(property="en", type="string", example="Enter your first name"), @OA\Property(property="de", type="string", example="Geben Sie Ihren Vornamen ein"))
     *             ),
     *             @OA\Property(
     *                 property="validation_rules",
     *                 type="object",
     *                 nullable=true
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Field created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Field"),
     *             @OA\Property(property="message", type="string", example="Field created successfully")
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
     *             @OA\Property(property="message", type="string", example="Failed to create field"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request, string $form_id): JsonResponse
    {
        try {
            $form = Auth::user()->forms()->findOrFail($form_id);

            $validated = $request->validate([
                'type' => 'required|string|in:text,email,password,number,textarea,select,checkbox,radio,file,date,time,datetime-local,url,tel,search,color,range,hidden',
                'order' => 'sometimes|integer|min:0',
                'configuration' => 'required|array',
                'configuration.type' => 'required|string|in:text,email,password,number,textarea,select,checkbox,radio,file,date,time,datetime-local,url,tel,search,color,range,hidden',
                'configuration.name' => 'required|string|max:255',
                'configuration.label' => 'required|array',
                'configuration.label.en' => 'required|string|max:255',
                'configuration.label.de' => 'required|string|max:255',
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
            ]);

            // Ensure type is also in configuration object as required by Field model validation
            $validated['configuration']['type'] = $validated['type'];

            // Set default order if not provided
            if (!isset($validated['order'])) {
                $validated['order'] = $form->fields()->max('order') + 1;
            }

            $field = $form->fields()->create($validated);

            return response()->json([
                'success' => true,
                'data' => $field,
                'message' => 'Field created successfully',
            ], 201);
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
                'message' => 'Failed to create field',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified field.
     *
     * @OA\Put(
     *     path="/api/forms/{form_id}/fields/{field_id}",
     *     operationId="updateField",
     *     tags={"Fields"},
     *     summary="Update a specific field",
     *     description="Update a specific field by ID for the authenticated user",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="form_id",
     *         in="path",
     *         description="Form ID",
     *         required=true,
     *
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *
     *     @OA\Parameter(
     *         name="field_id",
     *         in="path",
     *         description="Field ID",
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
     *             @OA\Property(property="type", type="string", enum={"text","email","password","number","textarea","select","checkbox","radio","file","date","time","datetime-local","url","tel","search","color","range","hidden"}),
     *             @OA\Property(property="order", type="integer", example=2),
     *             @OA\Property(
     *                 property="configuration",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="first_name"),
     *                 @OA\Property(
     *                     property="label",
     *                     type="object",
     *                     @OA\Property(property="en", type="string", example="First Name"),
     *                     @OA\Property(property="de", type="string", example="Vorname")
     *                 ),
     *                 @OA\Property(property="required", type="boolean", example=false)
     *             ),
     *             @OA\Property(
     *                 property="validation_rules",
     *                 type="object",
     *                 nullable=true
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Field updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Field"),
     *             @OA\Property(property="message", type="string", example="Field updated successfully")
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
     *         description="Form or field not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Form or field not found")
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
     *             @OA\Property(property="message", type="string", example="Failed to update field"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $form_id, string $field_id): JsonResponse
    {
        try {
            $form = Auth::user()->forms()->findOrFail($form_id);
            $field = $form->fields()->findOrFail($field_id);

            $validated = $request->validate([
                'type' => 'sometimes|string|in:text,email,password,number,textarea,select,checkbox,radio,file,date,time,datetime-local,url,tel,search,color,range,hidden',
                'order' => 'sometimes|integer|min:0',
                'configuration' => 'sometimes|array',
                'configuration.type' => 'sometimes|string|in:text,email,password,number,textarea,select,checkbox,radio,file,date,time,datetime-local,url,tel,search,color,range,hidden',
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
            ]);

            // Ensure type is also in configuration object when updating
            if (isset($validated['type'])) {
                $validated['configuration']['type'] = $validated['type'];
            }

            // Additional validation: if label is provided, both en and de must be present
            if (isset($validated['configuration']['label']) && is_array($validated['configuration']['label'])) {
                if (isset($validated['configuration']['label']['en']) && !isset($validated['configuration']['label']['de'])) {
                    throw ValidationException::withMessages([
                        'configuration.label.de' => ['The configuration.label.de field is required when configuration.label.en is present.'],
                    ]);
                }
                if (isset($validated['configuration']['label']['de']) && !isset($validated['configuration']['label']['en'])) {
                    throw ValidationException::withMessages([
                        'configuration.label.en' => ['The configuration.label.en field is required when configuration.label.de is present.'],
                    ]);
                }
            }

            $field->update($validated);

            return response()->json([
                'success' => true,
                'data' => $field->fresh(),
                'message' => 'Field updated successfully',
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
                'message' => 'Form or field not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update field',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
