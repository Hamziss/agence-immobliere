<?php

namespace App\Repositories;

use App\DTOs\CreatePropertyDTO;
use App\DTOs\FilterPropertiesDTO;
use App\DTOs\UpdatePropertyDTO;
use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PropertyRepository
{
  /**
   * Get all properties with filters and pagination
   */
  public function getAll(FilterPropertiesDTO $filters): LengthAwarePaginator
  {
    $query = Property::query()->with(['user', 'images', 'primaryImage']);

    // Apply filters
    if ($filters->onlyPublished) {
      $query->published();
    }

    if ($filters->city) {
      $query->inCity($filters->city);
    }

    if ($filters->type) {
      $query->ofType($filters->type);
    }

    if ($filters->status) {
      $query->withStatus($filters->status);
    }

    if ($filters->priceMin !== null || $filters->priceMax !== null) {
      $query->priceBetween($filters->priceMin, $filters->priceMax);
    }

    if ($filters->search) {
      $query->search($filters->search);
    }

    return $query->orderBy('created_at', 'desc')
      ->paginate($filters->perPage, ['*'], 'page', $filters->page);
  }

  /**
   * Get properties for a specific user
   */
  public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator
  {
    return Property::where('user_id', $userId)
      ->with(['images', 'primaryImage'])
      ->orderBy('created_at', 'desc')
      ->paginate($perPage);
  }

  /**
   * Find a property by ID
   */
  public function findById(int $id): ?Property
  {
    return Property::with(['user', 'images'])->find($id);
  }

  /**
   * Create a new property
   */
  public function create(CreatePropertyDTO $dto): Property
  {
    return Property::create($dto->toArray());
  }

  /**
   * Update a property
   */
  public function update(Property $property, UpdatePropertyDTO $dto): bool
  {
    return $property->update($dto->toArray());
  }

  /**
   * Delete a property (soft delete)
   */
  public function delete(Property $property): bool
  {
    return $property->delete();
  }

  /**
   * Force delete a property
   */
  public function forceDelete(Property $property): bool
  {
    return $property->forceDelete();
  }

  /**
   * Restore a soft deleted property
   */
  public function restore(int $id): bool
  {
    $property = Property::withTrashed()->find($id);
    if ($property) {
      return $property->restore();
    }
    return false;
  }

  /**
   * Get property statistics
   */
  public function getStatistics(int $userId = null): array
  {
    $query = Property::query();

    if ($userId) {
      $query->where('user_id', $userId);
    }

    return [
      'total' => $query->count(),
      'published' => (clone $query)->where('is_published', true)->count(),
      'disponible' => (clone $query)->where('status', 'disponible')->count(),
      'vendu' => (clone $query)->where('status', 'vendu')->count(),
      'location' => (clone $query)->where('status', 'location')->count(),
      'by_type' => (clone $query)->groupBy('type')
        ->selectRaw('type, count(*) as count')
        ->pluck('count', 'type')
        ->toArray(),
    ];
  }
}
