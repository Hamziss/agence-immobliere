<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="Modèle utilisateur",
 *     @OA\Property(property="id", type="integer", example=1, description="ID de l'utilisateur"),
 *     @OA\Property(property="name", type="string", example="John Doe", description="Nom de l'utilisateur"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com", description="Email de l'utilisateur"),
 *     @OA\Property(property="role", type="string", enum={"admin","agent","guest"}, example="agent", description="Rôle de l'utilisateur"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-11 18:30:00", description="Date de création")
 * )
 * 
 * @OA\Schema(
 *     schema="Property",
 *     title="Property",
 *     description="Modèle de bien immobilier",
 *     @OA\Property(property="id", type="integer", example=1, description="ID du bien"),
 *     @OA\Property(property="title", type="string", example="Villa 5 pièces - 250m² à Alger - Hydra", description="Titre généré automatiquement"),
 *     @OA\Property(property="type", type="string", enum={"appartement","villa","terrain","bureau","local_commercial"}, example="villa", description="Type de bien"),
 *     @OA\Property(property="rooms", type="integer", nullable=true, example=5, description="Nombre de pièces"),
 *     @OA\Property(property="surface", type="number", format="float", example=250.50, description="Surface en m²"),
 *     @OA\Property(property="price", type="number", format="float", example=45000000.00, description="Prix en DZD"),
 *     @OA\Property(property="city", type="string", example="Alger", description="Ville"),
 *     @OA\Property(property="district", type="string", nullable=true, example="Hydra", description="Quartier/Commune"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Belle villa moderne avec jardin", description="Description détaillée"),
 *     @OA\Property(property="status", type="string", enum={"disponible","vendu","location"}, example="disponible", description="Statut du bien"),
 *     @OA\Property(property="is_published", type="boolean", example=true, description="Publié ou non"),
 *     @OA\Property(property="user", ref="#/components/schemas/User", description="Propriétaire du bien"),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Image"),
 *         description="Images du bien"
 *     ),
 *     @OA\Property(property="primary_image", ref="#/components/schemas/Image", nullable=true, description="Image principale"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-11 18:30:00", description="Date de création"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-11 18:30:00", description="Date de modification")
 * )
 * 
 * @OA\Schema(
 *     schema="Image",
 *     title="Image",
 *     description="Modèle d'image",
 *     @OA\Property(property="id", type="integer", example=1, description="ID de l'image"),
 *     @OA\Property(property="filename", type="string", example="image.jpg", description="Nom du fichier"),
 *     @OA\Property(property="url", type="string", example="http://localhost:8000/storage/properties/1/image.jpg", description="URL complète de l'image"),
 *     @OA\Property(property="size", type="integer", example=1024000, description="Taille en bytes"),
 *     @OA\Property(property="is_primary", type="boolean", example=true, description="Image principale"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-11 18:30:00", description="Date de création")
 * )
 * 
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     title="Pagination Meta",
 *     description="Métadonnées de pagination",
 *     @OA\Property(property="current_page", type="integer", example=1, description="Page actuelle"),
 *     @OA\Property(property="from", type="integer", example=1, description="Premier élément"),
 *     @OA\Property(property="last_page", type="integer", example=5, description="Dernière page"),
 *     @OA\Property(property="per_page", type="integer", example=15, description="Éléments par page"),
 *     @OA\Property(property="to", type="integer", example=15, description="Dernier élément"),
 *     @OA\Property(property="total", type="integer", example=75, description="Total d'éléments")
 * )
 */
class SchemasController extends Controller
{
  //
}
