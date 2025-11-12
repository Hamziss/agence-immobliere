<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
  public function __construct(
    private readonly AuthService $authService
  ) {}

  /**
   * @OA\Post(
   *     path="/auth/register",
   *     summary="Inscrire un nouvel utilisateur",
   *     description="Crée un nouveau compte utilisateur. Seuls les rôles 'agent' et 'guest' sont autorisés via cette route. Les comptes admin doivent être créés directement en base de données.",
   *     operationId="register",
   *     tags={"Authentication"},
   *     @OA\RequestBody(
   *         required=true,
   *         description="Données d'inscription",
   *         @OA\JsonContent(
   *             required={"name","email","password","password_confirmation"},
   *             @OA\Property(property="name", type="string", example="John Doe", description="Nom complet de l'utilisateur"),
   *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="Adresse email unique"),
   *             @OA\Property(property="password", type="string", format="password", example="password123", description="Mot de passe (min 8 caractères)"),
   *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123", description="Confirmation du mot de passe"),
   *             @OA\Property(property="role", type="string", enum={"agent","guest"}, example="agent", description="Rôle de l'utilisateur (optionnel, défaut: guest). Le rôle 'admin' n'est PAS autorisé.")
   *         )
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="Inscription réussie",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="Inscription réussie."),
   *             @OA\Property(property="user", ref="#/components/schemas/User"),
   *             @OA\Property(property="token", type="string", example="1|xxxxxxxxxxxxxxxxxxxx", description="Token d'authentification Bearer")
   *         )
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Erreur de validation",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="Le rôle doit être agent ou guest. Les admins ne peuvent pas être créés via cette route."),
   *             @OA\Property(property="errors", type="object")
   *         )
   *     ),
   *     @OA\Response(
   *         response=400,
   *         description="Erreur lors de l'inscription"
   *     )
   * )
   */
  public function register(RegisterRequest $request): JsonResponse
  {
    try {
      $user = $this->authService->register(
        name: $request->input('name'),
        email: $request->input('email'),
        password: $request->input('password'),
        role: $request->input('role', 'guest')
      );


      $token = $user->createToken('auth-token')->plainTextToken;

      return response()->json([
        'message' => 'Inscription réussie.',
        'user' => new UserResource($user),
        'token' => $token,
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Erreur lors de l\'inscription.',
        'error' => $e->getMessage(),
      ], 400);
    }
  }

  /**
   * @OA\Post(
   *     path="/auth/login",
   *     summary="Connexion utilisateur",
   *     description="Authentifie un utilisateur et retourne un token Bearer",
   *     operationId="login",
   *     tags={"Authentication"},
   *     @OA\RequestBody(
   *         required=true,
   *         description="Identifiants de connexion",
   *         @OA\JsonContent(
   *             required={"email","password"},
   *             @OA\Property(property="email", type="string", format="email", example="agent1@digitup.com", description="Adresse email"),
   *             @OA\Property(property="password", type="string", format="password", example="password123", description="Mot de passe")
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Connexion réussie",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="Connexion réussie."),
   *             @OA\Property(property="user", ref="#/components/schemas/User"),
   *             @OA\Property(property="token", type="string", example="1|xxxxxxxxxxxxxxxxxxxx")
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Identifiants incorrects",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="Les identifiants sont incorrects.")
   *         )
   *     )
   * )
   */
  public function login(LoginRequest $request): JsonResponse
  {
    try {
      $result = $this->authService->login(
        email: $request->input('email'),
        password: $request->input('password')
      );

      return response()->json([
        'message' => 'Connexion réussie.',
        'user' => new UserResource($result['user']),
        'token' => $result['token'],
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Erreur lors de la connexion.',
        'error' => $e->getMessage(),
      ], 401);
    }
  }

  /**
   * @OA\Post(
   *     path="/auth/logout",
   *     summary="Déconnexion utilisateur",
   *     description="Révoque tous les tokens de l'utilisateur authentifié",
   *     operationId="logout",
   *     tags={"Authentication"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Response(
   *         response=200,
   *         description="Déconnexion réussie",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="Déconnexion réussie.")
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Non authentifié"
   *     )
   * )
   */
  public function logout(Request $request): JsonResponse
  {
    try {
      $this->authService->logout($request->user());

      return response()->json([
        'message' => 'Déconnexion réussie.',
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Erreur lors de la déconnexion.',
        'error' => $e->getMessage(),
      ], 400);
    }
  }

  /**
   * @OA\Get(
   *     path="/auth/me",
   *     summary="Obtenir le profil utilisateur",
   *     description="Retourne les informations de l'utilisateur authentifié",
   *     operationId="me",
   *     tags={"Authentication"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Response(
   *         response=200,
   *         description="Profil utilisateur",
   *         @OA\JsonContent(
   *             @OA\Property(property="user", ref="#/components/schemas/User")
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Non authentifié"
   *     )
   * )
   */
  public function me(Request $request): JsonResponse
  {
    return response()->json([
      'user' => new UserResource($request->user()),
    ]);
  }
}
