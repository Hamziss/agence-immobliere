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
   *     description="Permet au propriétaire ou à un admin d'ajouter des images à un bien. Une ou plusieurs images acceptées. Max 10 images, 5MB par image, formats: jpeg, png, webp.",
   *     operationId="uploadImages",
   *     tags={"Images"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="propertyId",
   *         in="path",
   *         description="ID du bien",
   *         required=true,
   *         @OA\Schema(type="integer", example=1)
   *     ),
   *     @OA\RequestBody(
   *         required=true,
   *         description="Envoyez un ou plusieurs fichiers dans le champ 'images'",
   *         @OA\MediaType(
   *             mediaType="multipart/form-data",
   *             @OA\Schema(
   *                 required={"images"},
   *                 @OA\Property(
   *                     property="images",
   *                     type="array",
   *                     description="Fichier(s) image(s) - Une seule image ou plusieurs (max 10)",
   *                     @OA\Items(
   *                         type="string",
   *                         format="binary"
   *                     )
   *                 )
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="Images téléchargées avec succès",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="Images téléchargées avec succès."),
   *             @OA\Property(
   *                 property="data",
   *                 type="array",
   *                 @OA\Items(ref="#/components/schemas/Image")
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Non authentifié",
   *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
   *     ),
   *     @OA\Response(
   *         response=403,
   *         description="Action non autorisée",
   *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Vous n'êtes pas autorisé à ajouter des images à ce bien."))
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Bien non trouvé",
   *         @OA\JsonContent(@OA\Property(property="message", type="string"))
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Erreur de validation (taille, format, nombre)",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string"),
   *             @OA\Property(property="errors", type="object")
   *         )
   *     )
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

      // Convertir en tableau si c'est un seul fichier
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
   *     description="Supprime une image. Seul le propriétaire du bien ou un admin peut supprimer.",
   *     operationId="deleteImage",
   *     tags={"Images"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="id",
   *         in="path",
   *         description="ID de l'image",
   *         required=true,
   *         @OA\Schema(type="integer", example=1)
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Image supprimée avec succès",
   *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Image supprimée avec succès."))
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Non authentifié",
   *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
   *     ),
   *     @OA\Response(
   *         response=403,
   *         description="Action non autorisée",
   *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Vous n'êtes pas autorisé à supprimer cette image."))
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Image non trouvée",
   *         @OA\JsonContent(@OA\Property(property="message", type="string"))
   *     )
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
   *     description="Marque une image comme image principale du bien. L'ancienne image principale perd automatiquement ce statut.",
   *     operationId="setPrimaryImage",
   *     tags={"Images"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="id",
   *         in="path",
   *         description="ID de l'image",
   *         required=true,
   *         @OA\Schema(type="integer", example=1)
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Image définie comme principale",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="Image définie comme principale avec succès."),
   *             @OA\Property(property="data", ref="#/components/schemas/Image")
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Non authentifié",
   *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
   *     ),
   *     @OA\Response(
   *         response=403,
   *         description="Action non autorisée",
   *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Vous n'êtes pas autorisé à modifier cette image."))
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Image non trouvée",
   *         @OA\JsonContent(@OA\Property(property="message", type="string"))
   *     )
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
