<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use App\Models\Property;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ImageController extends Controller
{
  public function __construct(
    private readonly ImageService $imageService
  ) {}

  /**
   * @OA\Post(
   *     path="/images/properties/{propertyId}",
   *     summary="Télécharger des images pour un bien",
   *     tags={"Images"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(name="propertyId", in="path", required=true, @OA\Schema(type="integer", example=1)),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\MediaType(
   *             mediaType="multipart/form-data",
   *             @OA\Schema(
   *                 required={"images"},
   *                 @OA\Property(
   *                     property="images",
   *                     type="array",
   *                     @OA\Items(type="string", format="binary")
   *                 )
   *             )
   *         )
   *     ),
   *     @OA\Response(response=201, description="Images téléchargées avec succès"),
   *     @OA\Response(response=401, description="Non authentifié"),
   *     @OA\Response(response=403, description="Action non autorisée"),
   *     @OA\Response(response=404, description="Bien non trouvé"),
   *     @OA\Response(response=422, description="Erreur de validation")
   * )
   */
  public function upload(UploadImageRequest $request, int $propertyId): JsonResponse
  {
    try {
      $property = Property::findOrFail($propertyId);

      // Check authorization
      if (!$request->user()->isAdmin() && $property->user_id !== $request->user()->id) {
        return response()->json([
          'message' => 'Vous n\'êtes pas autorisé à ajouter des images à ce bien.',
        ], 403);
      }


      $files = $request->file('images');
      if (!is_array($files)) {
        $files = [$files];
      }

      $images = $this->imageService->uploadMultipleImages(
        property: $property,
        files: $files
      );

      return response()->json([
        'message' => 'Images téléchargées avec succès.',
        'data' => ImageResource::collection($images),
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Erreur lors du téléchargement des images.',
        'error' => $e->getMessage(),
      ], 400);
    }
  }

  /**
   * @OA\Delete(
   *     path="/images/{id}",
   *     summary="Supprimer une image",
   *     tags={"Images"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
   *     @OA\Response(response=200, description="Image supprimée avec succès"),
   *     @OA\Response(response=401, description="Non authentifié"),
   *     @OA\Response(response=403, description="Action non autorisée"),
   *     @OA\Response(response=404, description="Image non trouvée")
   * )
   */
  public function destroy(Request $request, int $id): JsonResponse
  {
    try {
      $image = Image::findOrFail($id);

      $this->imageService->deleteImage($image, $request->user());

      return response()->json([
        'message' => 'Image supprimée avec succès.',
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Erreur lors de la suppression de l\'image.',
        'error' => $e->getMessage(),
      ], 400);
    }
  }

  /**
   * @OA\Post(
   *     path="/images/{id}/set-primary",
   *     summary="Définir une image comme principale",
   *     tags={"Images"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
   *     @OA\Response(response=200, description="Image définie comme principale"),
   *     @OA\Response(response=401, description="Non authentifié"),
   *     @OA\Response(response=403, description="Action non autorisée"),
   *     @OA\Response(response=404, description="Image non trouvée")
   * )
   */
  public function setPrimary(Request $request, int $id): JsonResponse
  {
    try {
      $image = Image::findOrFail($id);

      $this->imageService->setPrimaryImage($image, $request->user());

      return response()->json([
        'message' => 'Image définie comme principale avec succès.',
        'data' => new ImageResource($image->fresh()),
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Erreur lors de la modification de l\'image.',
        'error' => $e->getMessage(),
      ], 400);
    }
  }
}
