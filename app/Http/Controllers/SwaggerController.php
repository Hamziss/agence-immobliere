<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API Gestion de Biens Immobiliers - Digitup Company",
 *     version="1.0.0",
 *     description="API REST pour la gestion de biens immobiliers avec authentification par token et gestion des rôles (admin, agent, guest).",
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api",
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum",
 *     description="Utilisez le token obtenu lors de la connexion. Format: Bearer {token}"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints pour l'authentification (inscription, connexion, déconnexion)"
 * )
 * 
 * @OA\Tag(
 *     name="Properties",
 *     description="Gestion des biens immobiliers (CRUD, filtres, recherche)"
 * )
 * 
 * @OA\Tag(
 *     name="Images",
 *     description="Gestion des images des biens immobiliers"
 * )
 */
class SwaggerController extends Controller
{
  //
}
