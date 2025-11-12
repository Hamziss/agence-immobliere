<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
  /**
   * Determine whether the user can view any models.
   */
  public function viewAny(?User $user): bool
  {
    // Everyone can view the list (but filters apply based on role)
    return true;
  }

  /**
   * Determine whether the user can view the model.
   */
  public function view(?User $user, Property $property): bool
  {
    // Published properties can be viewed by anyone
    if ($property->is_published) {
      return true;
    }

    // Unpublished properties can only be viewed by the owner or admin
    if (!$user) {
      return false;
    }

    return $user->isAdmin() || $property->user_id === $user->id;
  }

  /**
   * Determine whether the user can create models.
   */
  public function create(User $user): bool
  {
    // Only admin and agent can create properties
    return $user->isAdmin() || $user->isAgent();
  }

  /**
   * Determine whether the user can update the model.
   */
  public function update(User $user, Property $property): bool
  {
    // Admin can update any property, agents can update only their own
    return $user->isAdmin() || $property->user_id === $user->id;
  }

  /**
   * Determine whether the user can delete the model.
   */
  public function delete(User $user, Property $property): bool
  {
    // Admin can delete any property, agents can delete only their own
    return $user->isAdmin() || $property->user_id === $user->id;
  }

  /**
   * Determine whether the user can restore the model.
   */
  public function restore(User $user, Property $property): bool
  {
    // Only admin can restore
    return $user->isAdmin();
  }

  /**
   * Determine whether the user can permanently delete the model.
   */
  public function forceDelete(User $user, Property $property): bool
  {
    // Only admin can force delete
    return $user->isAdmin();
  }
}
