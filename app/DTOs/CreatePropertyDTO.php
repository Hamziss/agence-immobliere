<?php

namespace App\DTOs;

class CreatePropertyDTO
{
  public function __construct(
    public readonly int $userId,
    public readonly string $type,
    public readonly ?int $rooms,
    public readonly float $surface,
    public readonly float $price,
    public readonly string $city,
    public readonly ?string $district,
    public readonly ?string $description,
    public readonly string $status = 'disponible',
    public readonly bool $isPublished = false,
  ) {}

  public function toArray(): array
  {
    return [
      'user_id' => $this->userId,
      'type' => $this->type,
      'rooms' => $this->rooms,
      'surface' => $this->surface,
      'price' => $this->price,
      'city' => $this->city,
      'district' => $this->district,
      'description' => $this->description,
      'status' => $this->status,
      'is_published' => $this->isPublished,
    ];
  }
}
