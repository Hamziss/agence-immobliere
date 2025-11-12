<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PropertyCollection extends ResourceCollection
{
  /**
   * Transform the resource collection into an array.
   *
   * @return array<int|string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'data' => $this->collection,
      'meta' => [
        'current_page' => $this->currentPage(),
        'from' => $this->firstItem(),
        'last_page' => $this->lastPage(),
        'per_page' => $this->perPage(),
        'to' => $this->lastItem(),
        'total' => $this->total(),
      ],
    ];
  }
}
