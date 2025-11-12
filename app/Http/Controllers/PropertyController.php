<?php

namespace App\Http\Controllers;

use App\DTOs\CreatePropertyDTO;
use App\DTOs\FilterPropertiesDTO;
use App\DTOs\UpdatePropertyDTO;
use App\Http\Requests\FilterPropertiesRequest;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyCollection;
use App\Http\Resources\PropertyResource;
use App\Services\PropertyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class PropertyController extends Controller
{
  public function __construct(
    private readonly PropertyService $propertyService
  ) {}

  /**
   * @OA\Get(
   *     path="/properties",
   *     summary="Liste des biens immobiliers",
   *     tags={"Properties"},
   *     @OA\Parameter(name="city", in="query", required=false, @OA\Schema(type="string", example="Alger")),
   *     @OA\Parameter(name="type", in="query", required=false, @OA\Schema(type="string", enum={"appartement","villa","terrain","bureau","local_commercial"}, example="appartement")),
   *     @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string", enum={"disponible","vendu","location"}, example="disponible")),
   *     @OA\Parameter(name="price_min", in="query", required=false, @OA\Schema(type="number", example=10000000)),
   *     @OA\Parameter(name="price_max", in="query", required=false, @OA\Schema(type="number", example=20000000)),
   *     @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string", example="vue mer")),
   *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15, example=10)),
   *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1, example=1)),
   *     @OA\Response(response=200, description="Liste des biens récupérée avec succès")
   * )
   */
  public function index(Request $request): JsonResponse
  {
    // DTO layer
    $filters = new FilterPropertiesDTO(
      city: $request->input('city'),
      type: $request->input('type'),
      priceMin: $request->input('price_min') ? (float) $request->input('price_min') : null,
      priceMax: $request->input('price_max') ? (float) $request->input('price_max') : null,
      status: $request->input('status'),
      search: $request->input('search'),
      perPage: $request->input('per_page', 15),
      page: $request->input('page', 1),
    );

    // Service layer
    $properties = $this->propertyService->getAllProperties($filters, $request->user());

    return response()->json([
      'message' => 'Liste des biens récupérée avec succès.',
      'data' => PropertyResource::collection($properties),
      'meta' => [
        'current_page' => $properties->currentPage(),
        'from' => $properties->firstItem(),
        'last_page' => $properties->lastPage(),
        'per_page' => $properties->perPage(),
        'to' => $properties->lastItem(),
        'total' => $properties->total(),
      ],
    ]);
  }

  /**
   * @OA\Post(
   *     path="/properties",
   *     summary="Créer un nouveau bien",
   *     tags={"Properties"},
   *     security={{"bearerAuth":{}}},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"type","rooms","surface","price","city","district","description"},
   *             @OA\Property(property="type", type="string", enum={"appartement","villa","terrain","bureau","local_commercial"}, example="appartement"),
   *             @OA\Property(property="rooms", type="integer", example=3),
   *             @OA\Property(property="surface", type="number", format="float", example=85.5),
   *             @OA\Property(property="price", type="number", format="float", example=15000000),
   *             @OA\Property(property="city", type="string", example="Alger"),
   *             @OA\Property(property="district", type="string", example="Hydra"),
   *             @OA\Property(property="description", type="string", example="Bel appartement avec vue sur mer"),
   *             @OA\Property(property="status", type="string", enum={"disponible","vendu","location"}, example="disponible"),
   *             @OA\Property(property="is_published", type="boolean", example=true)
   *         )
   *     ),
   *     @OA\Response(response=201, description="Bien créé avec succès"),
   *     @OA\Response(response=401, description="Non authentifié"),
   *     @OA\Response(response=403, description="Action non autorisée"),
   *     @OA\Response(response=422, description="Erreur de validation")
   * )
   */
  public function store(StorePropertyRequest $request): JsonResponse
  {
    try {
      $this->authorize('create', \App\Models\Property::class);

      // DTO layer
      $dto = new CreatePropertyDTO(
        userId: $request->user()->id,
        type: $request->input('type'),
        rooms: $request->input('rooms'),
        surface: (float) $request->input('surface'),
        price: (float) $request->input('price'),
        city: $request->input('city'),
        district: $request->input('district'),
        description: $request->input('description'),
        status: $request->input('status', 'disponible'),
        isPublished: $request->input('is_published', false),
      );

      // Service layer
      $property = $this->propertyService->createProperty($dto);

      return response()->json([
        'message' => 'Bien créé avec succès.',
        'data' => new PropertyResource($property->load(['user', 'images'])),
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Erreur lors de la création du bien.',
        'error' => $e->getMessage(),
      ], 400);
    }
  }

  /**
   * @OA\Get(
   *     path="/properties/{id}",
   *     summary="Afficher un bien spécifique",
   *     tags={"Properties"},
   *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
   *     @OA\Response(response=200, description="Détails du bien récupérés"),
   *     @OA\Response(response=404, description="Bien non trouvé"),
   *     @OA\Response(response=403, description="Accès non autorisé")
   * )
   */
  public function show(Request $request, int $id): JsonResponse
  {
    try {
      $property = $this->propertyService->getPropertyById($id, $request->user());

      if (!$property) {
        return response()->json([
          'message' => 'Bien non trouvé.',
        ], 404);
      }

      $this->authorize('view', $property);

      return response()->json([
        'message' => 'Détails du bien récupérés avec succès.',
        'data' => new PropertyResource($property),
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Erreur lors de la récupération du bien.',
        'error' => $e->getMessage(),
      ], 400);
    }
  }

  /**
   * @OA\Put(
   *     path="/properties/{id}",
   *     summary="Mettre à jour un bien",
   *     tags={"Properties"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
   *     @OA\RequestBody(
   *         required=false,
   *         @OA\JsonContent(
   *             @OA\Property(property="type", type="string", enum={"appartement","villa","terrain","bureau","local_commercial"}, example="villa"),
   *             @OA\Property(property="rooms", type="integer", example=5),
   *             @OA\Property(property="surface", type="number", format="float", example=150.0),
   *             @OA\Property(property="price", type="number", format="float", example=25000000),
   *             @OA\Property(property="city", type="string", example="Oran"),
   *             @OA\Property(property="district", type="string", example="Les Planteurs"),
   *             @OA\Property(property="description", type="string", example="Villa rénovée avec jardin"),
   *             @OA\Property(property="status", type="string", enum={"disponible","vendu","location"}, example="disponible"),
   *             @OA\Property(property="is_published", type="boolean", example=true)
   *         )
   *     ),
   *     @OA\Response(response=200, description="Bien mis à jour avec succès"),
   *     @OA\Response(response=401, description="Non authentifié"),
   *     @OA\Response(response=403, description="Action non autorisée"),
   *     @OA\Response(response=404, description="Bien non trouvé")
   * )
   */
  public function update(UpdatePropertyRequest $request, int $id): JsonResponse
  {
    try {
      $property = $this->propertyService->getPropertyById($id, $request->user());

      if (!$property) {
        return response()->json([
          'message' => 'Bien non trouvé.',
        ], 404);
      }

      $this->authorize('update', $property);

      $dto = new UpdatePropertyDTO(
        type: $request->input('type'),
        rooms: $request->input('rooms'),
        surface: $request->has('surface') ? (float) $request->input('surface') : null,
        price: $request->has('price') ? (float) $request->input('price') : null,
        city: $request->input('city'),
        district: $request->input('district'),
        description: $request->input('description'),
        status: $request->input('status'),
        isPublished: $request->input('is_published'),
      );

      $this->propertyService->updateProperty($property, $dto, $request->user());

      return response()->json([
        'message' => 'Bien mis à jour avec succès.',
        'data' => new PropertyResource($property->fresh(['user', 'images'])),
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Erreur lors de la mise à jour du bien.',
        'error' => $e->getMessage(),
      ], 400);
    }
  }

  /**
   * @OA\Delete(
   *     path="/properties/{id}",
   *     summary="Supprimer un bien",
   *     tags={"Properties"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
   *     @OA\Response(response=200, description="Bien supprimé avec succès"),
   *     @OA\Response(response=401, description="Non authentifié"),
   *     @OA\Response(response=403, description="Action non autorisée"),
   *     @OA\Response(response=404, description="Bien non trouvé")
   * )
   */
  public function destroy(Request $request, int $id): JsonResponse
  {
    try {
      $property = $this->propertyService->getPropertyById($id, $request->user());

      if (!$property) {
        return response()->json([
          'message' => 'Bien non trouvé.',
        ], 404);
      }

      $this->authorize('delete', $property);

      $this->propertyService->deleteProperty($property, $request->user());

      return response()->json([
        'message' => 'Bien supprimé avec succès.',
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Erreur lors de la suppression du bien.',
        'error' => $e->getMessage(),
      ], 400);
    }
  }

  /**
   * @OA\Post(
   *     path="/properties/{id}/toggle-publish",
   *     summary="Basculer le statut de publication",
   *     tags={"Properties"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
   *     @OA\Response(response=200, description="Statut modifié avec succès"),
   *     @OA\Response(response=401, description="Non authentifié"),
   *     @OA\Response(response=403, description="Action non autorisée"),
   *     @OA\Response(response=404, description="Bien non trouvé")
   * )
   */
  public function togglePublish(Request $request, int $id): JsonResponse
  {
    try {
      $property = $this->propertyService->getPropertyById($id, $request->user());

      if (!$property) {
        return response()->json([
          'message' => 'Bien non trouvé.',
        ], 404);
      }

      $this->authorize('update', $property);

      $this->propertyService->togglePublishStatus($property, $request->user());

      return response()->json([
        'message' => 'Statut de publication modifié avec succès.',
        'data' => new PropertyResource($property->fresh(['user', 'images'])),
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Erreur lors de la modification du statut.',
        'error' => $e->getMessage(),
      ], 400);
    }
  }
}
