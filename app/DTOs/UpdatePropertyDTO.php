<?php

namespace App\DTOs;

class UpdatePropertyDTO
{
  public function __construct(
    public readonly ?string $type = null,
    public readonly ?int $rooms = null,
    public readonly ?float $surface = null,
    public readonly ?float $price = null,
    public readonly ?string $city = null,
    public readonly ?string $district = null,
    public readonly ?string $description = null,
    public readonly ?string $status = null,
    public readonly ?bool $isPublished = null,
  ) {}

  public function toArray(): array
  {
    $data = [];

    if ($this->type !== null) {
      $data['type'] = $this->type;
    }
    if ($this->rooms !== null) {
      $data['rooms'] = $this->rooms;
    }
    if ($this->surface !== null) {
      $data['surface'] = $this->surface;
    }
    if ($this->price !== null) {
      $data['price'] = $this->price;
    }
    if ($this->city !== null) {
      $data['city'] = $this->city;
    }
    if ($this->district !== null) {
      $data['district'] = $this->district;
    }
    if ($this->description !== null) {
      $data['description'] = $this->description;
    }
    if ($this->status !== null) {
      $data['status'] = $this->status;
    }
    if ($this->isPublished !== null) {
      $data['is_published'] = $this->isPublished;
    }

    return $data;
  }
}
