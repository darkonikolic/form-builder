<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ServerException;
use App\Http\Requests\Field\StoreFieldRequest;
use App\Http\Requests\Field\UpdateFieldRequest;
use App\Services\FieldService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Fields",
 *     description="Form field management endpoints"
 * )
 */
class FieldController extends Controller
{
    public function __construct(
        private FieldService $fieldService,
        private ResponseService $responseService,
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Remove the specified field.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
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
        $this->fieldService->deleteFormField(Auth::user(), $form_id, $field_id);

        return $this->responseService->successResponse(null, 'Field deleted successfully');
    }

    /**
     * Display a listing of fields for a specific form.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
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
        $fields = $this->fieldService->getFormFields(Auth::user(), $form_id);

        return $this->responseService->successResponse($fields, 'Fields retrieved successfully');
    }

    /**
     * Display the specified field.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
     *
     * @OA\Get(
     *     path="/api/forms/{form_id}/fields/{field_id}",
     *     operationId="showField",
     *     tags={"Fields"},
     *     summary="Get a specific field",
     *     description="Get a specific field by ID for the authenticated user",
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
     *         description="Field not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Field not found")
     *         )
     *     )
     * )
     */
    public function show(string $form_id, string $field_id): JsonResponse
    {
        $field = $this->fieldService->getFormField(Auth::user(), $form_id, $field_id);

        return $this->responseService->successResponse($field, 'Field retrieved successfully');
    }

    /**
     * Store a newly created field.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
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
    public function store(StoreFieldRequest $request, string $form_id): JsonResponse
    {
        $validated = $request->validated();
        $field = $this->fieldService->createFormField(Auth::user(), $form_id, $validated);

        return $this->responseService->successResponse($field, 'Field created successfully', 201);
    }

    /**
     * Update the specified field.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
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
     *             @OA\Property(
     *                 property="type",
     *                 type="string",
     *                 enum={"text","email","password","number","textarea","select","checkbox","radio","file","date","time","datetime-local","url","tel","search","color","range","hidden"},
     *                 example="text"
     *             ),
     *             @OA\Property(property="order", type="integer", example=1),
     *             @OA\Property(
     *                 property="configuration",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="field_name"),
     *                 @OA\Property(
     *                     property="label",
     *                     type="object",
     *                     @OA\Property(property="en", type="string", example="Field Label"),
     *                     @OA\Property(property="de", type="string", example="Feld Bezeichnung")
     *                 ),
     *                 @OA\Property(property="required", type="boolean", example=true)
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
    public function update(UpdateFieldRequest $request, string $form_id, string $field_id): JsonResponse
    {
        $validated = $request->validated();
        $field = $this->fieldService->updateFormField(Auth::user(), $form_id, $field_id, $validated);

        return $this->responseService->successResponse($field, 'Field updated successfully');
    }
}
