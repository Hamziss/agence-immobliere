<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API Gestion de Biens Immobiliers - Digitup Company",
 *     version="1.0.0",
 *     description="
API RESTful pour la gestion d'une agence immobilière avec système d'authentification basé sur des rôles.

## 👤 Utilisateurs de test
| Rôle  | Email                | Mot de passe  |
|-------|---------------------|---------------|
| Admin | admin@digitup.com   | password123   |
| Agent | agent1@digitup.com  | password123   |
| Agent | agent2@digitup.com  | password123   |
| Guest | guest@digitup.com   | password123   |
"
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum"
 * )
 * 
 * @OA\Tag(name="Authentication")
 * @OA\Tag(name="Properties")
 * @OA\Tag(name="Images")
 */
class SwaggerController extends Controller
{
  //
}
