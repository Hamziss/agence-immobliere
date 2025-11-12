<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="User",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="role", type="string", enum={"admin","agent","guest"}, example="agent"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-11 18:30:00")
 * )
 * 
 * @OA\Schema(
 *     schema="Property",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Villa 5 pièces - 250m² à Alger - Hydra"),
 *     @OA\Property(property="type", type="string", enum={"appartement","villa","terrain","bureau","local_commercial"}, example="villa"),
 *     @OA\Property(property="rooms", type="integer", nullable=true, example=5),
 *     @OA\Property(property="surface", type="number", format="float", example=250.50),
 *     @OA\Property(property="price", type="number", format="float", example=45000000.00),
 *     @OA\Property(property="city", type="string", example="Alger"),
 *     @OA\Property(property="district", type="string", nullable=true, example="Hydra"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Belle villa moderne avec jardin"),
 *     @OA\Property(property="status", type="string", enum={"disponible","vendu","location"}, example="disponible"),
 *     @OA\Property(property="is_published", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-11 18:30:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-11 18:30:00")
 * )
 * 
 * @OA\Schema(
 *     schema="Image",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="filename", type="string", example="image.jpg"),
 *     @OA\Property(property="url", type="string", example="http://localhost:8000/storage/properties/1/image.jpg"),
 *     @OA\Property(property="size", type="integer", example=1024000),
 *     @OA\Property(property="is_primary", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-11 18:30:00")
 * )
 */
class SchemasController extends Controller
{
  //
}
