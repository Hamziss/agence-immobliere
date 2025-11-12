<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API Gestion de Biens Immobiliers - Digitup Company",
 *     version="1.0.0"
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
