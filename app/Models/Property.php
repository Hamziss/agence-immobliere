<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
  use HasFactory, SoftDeletes; // ulisation du Soft Delete

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'user_id',
    'type',
    'rooms',
    'surface',
    'price',
    'city',
    'district',
    'description',
    'status',
    'is_published',
    'title',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'rooms' => 'integer',
    'surface' => 'decimal:2',
    'price' => 'decimal:2',
    'is_published' => 'boolean',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
  ];

  /**
   * The "booted" method of the model.
   */
  protected static function booted(): void
  {
    static::creating(function (Property $property) {
      if (empty($property->title)) {
        $property->title = $property->generateTitle();
      }
    });

    static::updating(function (Property $property) {
      if ($property->isDirty(['type', 'rooms', 'city', 'district']) || empty($property->title)) {
        $property->title = $property->generateTitle();
      }
    });
  }

  /**
   * Generate automatic title for property
   */
  public function generateTitle(): string
  {
    $typeLabels = [
      'appartement' => 'Appartement',
      'villa' => 'Villa',
      'terrain' => 'Terrain',
      'bureau' => 'Bureau',
      'local_commercial' => 'Local Commercial',
    ];

    $typeName = $typeLabels[$this->type] ?? ucfirst($this->type);
    $title = $typeName;

    if ($this->rooms && in_array($this->type, ['appartement', 'villa', 'bureau'])) {
      $title .= ' ' . $this->rooms . ' pièces';
    }

    if ($this->surface) {
      $title .= ' - ' . number_format($this->surface, 0) . 'm²';
    }

    $location = $this->city;
    if ($this->district) {
      $location .= ' - ' . $this->district;
    }
    $title .= ' à ' . $location;

    return $title;
  }

  /**
   * Get the user that owns the property
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Get the images for this property
   */
  public function images(): HasMany
  {
    return $this->hasMany(Image::class);
  }

  /**
   * Get the primary image
   */
  public function primaryImage()
  {
    return $this->hasOne(Image::class)->where('is_primary', true);
  }

  /**
   * Scope a query to only include published properties
   */
  public function scopePublished($query)
  {
    return $query->where('is_published', true);
  }

  /**
   * Scope a query to filter by type
   */
  public function scopeOfType($query, string $type)
  {
    return $query->where('type', $type);
  }

  /**
   * Scope a query to filter by city
   */
  public function scopeInCity($query, string $city)
  {
    return $query->where('city', 'like', '%' . $city . '%');
  }

  /**
   * Scope a query to filter by status
   */
  public function scopeWithStatus($query, string $status)
  {
    return $query->where('status', $status);
  }

  /**
   * Scope a query to filter by price range
   */
  public function scopePriceBetween($query, ?float $min, ?float $max)
  {
    if ($min !== null) {
      $query->where('price', '>=', $min);
    }
    if ($max !== null) {
      $query->where('price', '<=', $max);
    }
    return $query;
  }

  /**
   * Scope a query to search in title and description
   */
  public function scopeSearch($query, ?string $search)
  {
    if ($search) {
      return $query->where(function ($q) use ($search) {
        $q->where('title', 'like', '%' . $search . '%')
          ->orWhere('description', 'like', '%' . $search . '%')
          ->orWhere('city', 'like', '%' . $search . '%');
      });
    }
    return $query;
  }
}
