<?php

namespace App\Repositories;

use App\Models\Image;
use App\Models\Property;
use Illuminate\Support\Collection;

class ImageRepository
{
  /**
   * Get all images for a property
   */
  public function getByProperty(int $propertyId): Collection
  {
    return Image::where('property_id', $propertyId)
      ->orderBy('is_primary', 'desc')
      ->orderBy('created_at', 'asc')
      ->get();
  }

  /**
   * Find an image by ID
   */
  public function findById(int $id): ?Image
  {
    return Image::find($id);
  }

  /**
   * Create a new image
   */
  public function create(array $data): Image
  {
    return Image::create($data);
  }

  /**
   * Delete an image
   */
  public function delete(Image $image): bool
  {
    return $image->delete();
  }

  /**
   * Set an image as primary
   */
  public function setPrimary(Image $image): bool
  {
    // Remove primary flag from other images of the same property
    Image::where('property_id', $image->property_id)
      ->where('id', '!=', $image->id)
      ->update(['is_primary' => false]);

    // Set this image as primary
    return $image->update(['is_primary' => true]);
  }

  /**
   * Delete all images for a property
   */
  public function deleteByProperty(int $propertyId): int
  {
    return Image::where('property_id', $propertyId)->delete();
  }
}
