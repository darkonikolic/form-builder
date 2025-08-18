<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ServerException;
use App\Http\Requests\Form\StoreFormRequest;
use App\Http\Requests\Form\UpdateFormRequest;
use App\Services\FormService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Forms",
 *     description="Form management endpoints"
 * )
 */
class FormController extends Controller
{
    public function __construct(
        private FormService $formService,
        private ResponseService $responseService,
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Remove the specified form.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
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
        $this->formService->deleteForm(Auth::user()->id, $id);

        return $this->responseService->successResponse(null, 'Form deleted successfully');
    }

    /**
     * Display a listing of the user's forms.
     *
     * @throws ServerException
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
        $forms = $this->formService->getUserForms(Auth::user()->id);

        return $this->responseService->successResponse($forms, 'Forms retrieved successfully');
    }

    /**
     * Display the specified form.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
     *
     * @OA\Get(
     *     path="/api/forms/{id}",
     *     operationId="showForm",
     *     tags={"Forms"},
     *     summary="Get a specific form",
     *     description="Get a specific form by ID for the authenticated user",
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
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $form = $this->formService->getUserFormWithFields(Auth::user(), $id);

        return $this->responseService->successResponse($form, 'Form retrieved successfully');
    }

    /**
     * Store a newly created form.
     *
     * @throws ServerException
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
     *             @OA\Property(property="de", type="string", maxLength=1000, example="Ein Kontaktformular für Kundenanfragen")
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
    public function store(StoreFormRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $form = $this->formService->createForm(Auth::user()->id, $validated);

        return $this->responseService->successResponse($form, 'Form created successfully', 201);
    }

    /**
     * Update the specified form.
     *
     * @throws ResourceNotFoundException
     * @throws ServerException
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
     *                 @OA\Property(property="en", type="string", example="Updated Form Name"),
     *                 @OA\Property(property="de", type="string", example="Aktualisierter Formularname")
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="object",
     *                 @OA\Property(property="en", type="string", example="Updated form description"),
     *                 @OA\Property(property="de", type="string", example="Aktualisierte Formularbeschreibung")
     *             ),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(
     *                 property="configuration",
     *                 type="object",
     *                 @OA\Property(
     *                     property="locales",
     *                     type="array",
     *
     *                     @OA\Items(type="string", enum={"en", "de", "it", "fr"}),
     *                     example={"en", "de"}
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
    public function update(UpdateFormRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();
        $form = $this->formService->updateForm(Auth::user()->id, $id, $validated);

        return $this->responseService->successResponse($form, 'Form updated successfully');
    }
}
