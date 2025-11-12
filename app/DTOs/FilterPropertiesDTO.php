<?php

namespace App\DTOs;

class FilterPropertiesDTO
{
  public function __construct(
    public readonly ?string $city = null,
    public readonly ?string $type = null,
    public readonly ?float $priceMin = null,
    public readonly ?float $priceMax = null,
    public readonly ?string $status = null,
    public readonly ?string $search = null,
    public readonly int $perPage = 15,
    public readonly int $page = 1,
    public readonly bool $onlyPublished = false,
  ) {}
}
