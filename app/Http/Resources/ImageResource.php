<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'filename' => $this->filename,
      'url' => asset('storage/' . $this->path),
      'size' => $this->size,
      'is_primary' => $this->is_primary,
      'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
    ];
  }
}
