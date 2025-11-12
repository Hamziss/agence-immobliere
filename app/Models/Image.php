<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'property_id',
    'path',
    'filename',
    'size',
    'mime_type',
    'is_primary',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'size' => 'integer',
    'is_primary' => 'boolean',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  /**
   * Get the property that owns the image
   */
  public function property(): BelongsTo
  {
    return $this->belongsTo(Property::class);
  }

  /**
   * Get the full URL of the image
   */
  public function getUrlAttribute(): string
  {
    return asset('storage/' . $this->path);
  }
}
