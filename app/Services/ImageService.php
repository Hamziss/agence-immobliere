<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Property;
use App\Models\User;
use App\Repositories\ImageRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ImageService
{
  public function __construct(
    private readonly ImageRepository $imageRepository
  ) {}

  /**
   * Get all images for a property
   */
  public function getPropertyImages(int $propertyId): Collection
  {
    return $this->imageRepository->getByProperty($propertyId);
  }

  /**
   * Upload and save an image
   */
  public function uploadImage(Property $property, UploadedFile $file, bool $isPrimary = false): Image
  {
    // Validate file
    $this->validateImage($file);

    // If this is set as primary, remove primary flag from other images
    if ($isPrimary) {
      Image::where('property_id', $property->id)
        ->update(['is_primary' => false]);
    }

    // Store file
    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    $path = $file->storeAs('properties/' . $property->id, $filename, 'public');

    // Create image record
    return $this->imageRepository->create([
      'property_id' => $property->id,
      'path' => $path,
      'filename' => $file->getClientOriginalName(),
      'size' => $file->getSize(),
      'mime_type' => $file->getMimeType(),
      'is_primary' => $isPrimary,
    ]);
  }

  /**
   * Upload multiple images
   */
  public function uploadMultipleImages(Property $property, array $files): Collection
  {
    $images = collect();

    foreach ($files as $index => $file) {
      // First image is primary if no primary image exists
      $isPrimary = $index === 0 && !$property->images()->where('is_primary', true)->exists();
      $images->push($this->uploadImage($property, $file, $isPrimary));
    }

    return $images;
  }

  /**
   * Delete an image
   */
  public function deleteImage(Image $image, User $user): bool
  {
    $property = $image->property;

    // Check authorization
    if (!$user->isAdmin() && $property->user_id !== $user->id) {
      throw new \Exception('Vous n\'êtes pas autorisé à supprimer cette image.');
    }

    // Delete file from storage
    if (Storage::disk('public')->exists($image->path)) {
      Storage::disk('public')->delete($image->path);
    }

    // Delete record
    return $this->imageRepository->delete($image);
  }

  /**
   * Set an image as primary
   */
  public function setPrimaryImage(Image $image, User $user): bool
  {
    $property = $image->property;

    // Check authorization
    if (!$user->isAdmin() && $property->user_id !== $user->id) {
      throw new \Exception('Vous n\'êtes pas autorisé à modifier cette image.');
    }

    return $this->imageRepository->setPrimary($image);
  }

  /**
   * Validate image file
   */
  private function validateImage(UploadedFile $file): void
  {
    $maxSize = 5 * 1024 * 1024; // 5MB
    $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

    if ($file->getSize() > $maxSize) {
      throw new \Exception('La taille de l\'image ne doit pas dépasser 5MB.');
    }

    if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
      throw new \Exception('Le format de l\'image n\'est pas autorisé. Formats autorisés : JPEG, PNG, WebP.');
    }
  }
}
