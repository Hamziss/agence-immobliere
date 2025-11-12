<?php

namespace App\Services;

use App\DTOs\CreatePropertyDTO;
use App\DTOs\FilterPropertiesDTO;
use App\DTOs\UpdatePropertyDTO;
use App\Models\Property;
use App\Models\User;
use App\Repositories\PropertyRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class PropertyService
{
  public function __construct(
    private readonly PropertyRepository $propertyRepository
  ) {}

  /**
   * Get all properties with filters
   */
  public function getAllProperties(FilterPropertiesDTO $filters, ?User $user = null): LengthAwarePaginator
  {
    // If user is guest or not authenticated, only show published properties
    if (!$user || $user->isGuest()) {
      $filters = new FilterPropertiesDTO(
        city: $filters->city,
        type: $filters->type,
        priceMin: $filters->priceMin,
        priceMax: $filters->priceMax,
        status: $filters->status,
        search: $filters->search,
        perPage: $filters->perPage,
        page: $filters->page,
        onlyPublished: true,
      );
    }

    return $this->propertyRepository->getAll($filters);
  }

  /**
   * Get properties for a specific user
   */
  public function getUserProperties(int $userId, int $perPage = 15): LengthAwarePaginator
  {
    return $this->propertyRepository->getByUser($userId, $perPage);
  }

  /**
   * Get property by ID
   */
  public function getPropertyById(int $id, ?User $user = null): ?Property
  {
    $property = $this->propertyRepository->findById($id);

    if (!$property) {
      return null;
    }

    // If user is guest or not authenticated, only show published properties
    if ((!$user || $user->isGuest()) && !$property->is_published) {
      return null;
    }

    // Check if user can view this property
    if ($user && !$user->isAdmin() && !$user->isGuest() && $property->user_id !== $user->id && !$property->is_published) {
      return null;
    }

    return $property;
  }

  /**
   * Create a new property
   */
  public function createProperty(CreatePropertyDTO $dto): Property
  {
    return $this->propertyRepository->create($dto);
  }

  /**
   * Update a property
   */
  public function updateProperty(Property $property, UpdatePropertyDTO $dto, User $user): bool
  {
    // Check authorization
    if (!$user->isAdmin() && $property->user_id !== $user->id) {
      throw new \Exception('Vous n\'êtes pas autorisé à modifier ce bien.');
    }

    return $this->propertyRepository->update($property, $dto);
  }

  /**
   * Delete a property
   */
  public function deleteProperty(Property $property, User $user): bool
  {
    // Check authorization
    if (!$user->isAdmin() && $property->user_id !== $user->id) {
      throw new \Exception('Vous n\'êtes pas autorisé à supprimer ce bien.');
    }

    return $this->propertyRepository->delete($property);
  }

  /**
   * Get property statistics
   */
  public function getStatistics(User $user): array
  {
    // Admin can see all statistics, agents only their own
    $userId = $user->isAdmin() ? null : $user->id;
    return $this->propertyRepository->getStatistics($userId);
  }

  /**
   * Publish or unpublish a property
   */
  public function togglePublishStatus(Property $property, User $user): bool
  {
    // Check authorization
    if (!$user->isAdmin() && $property->user_id !== $user->id) {
      throw new \Exception('Vous n\'êtes pas autorisé à modifier ce bien.');
    }

    $dto = new UpdatePropertyDTO(isPublished: !$property->is_published);
    return $this->propertyRepository->update($property, $dto);
  }
}
