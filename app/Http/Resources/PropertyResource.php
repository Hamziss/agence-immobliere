<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
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
      'title' => $this->title,
      'type' => $this->type,
      'rooms' => $this->rooms,
      'surface' => (float) $this->surface,
      'price' => (float) $this->price,
      'city' => $this->city,
      'district' => $this->district,
      'description' => $this->description,
      'status' => $this->status,
      'is_published' => $this->is_published,
      'user' => new UserResource($this->whenLoaded('user')),
      'images' => ImageResource::collection($this->whenLoaded('images')),
      'primary_image' => new ImageResource($this->whenLoaded('primaryImage')),
      'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
      'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
    ];
  }
}
