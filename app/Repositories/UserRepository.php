<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
  /**
   * Créer un nouvel utilisateur
   */
  public function create(array $data): User
  {
    return User::create($data);
  }

  /**
   * Trouver un utilisateur par email
   */
  public function findByEmail(string $email): ?User
  {
    return User::where('email', $email)->first();
  }

  /**
   * Trouver un utilisateur par ID
   */
  public function findById(int $id): ?User
  {
    return User::find($id);
  }

  /**
   * Mettre à jour un utilisateur
   */
  public function update(User $user, array $data): bool
  {
    return $user->update($data);
  }

  /**
   * Supprimer un utilisateur
   */
  public function delete(User $user): bool
  {
    return $user->delete();
  }

  /**
   * Récupérer tous les utilisateurs
   */
  public function getAll(): Collection
  {
    return User::all();
  }

  /**
   * Récupérer les utilisateurs par rôle
   */
  public function getByRole(string $role): Collection
  {
    return User::where('role', $role)->get();
  }

  /**
   * Vérifier si un email existe déjà
   */
  public function emailExists(string $email): bool
  {
    return User::where('email', $email)->exists();
  }
}
