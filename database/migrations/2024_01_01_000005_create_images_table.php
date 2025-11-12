<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('images', function (Blueprint $table) {
      $table->id();
      $table->foreignId('property_id')->constrained()->onDelete('cascade');
      $table->string('path');
      $table->string('filename');
      $table->integer('size')->comment('Taille en bytes');
      $table->string('mime_type');
      $table->boolean('is_primary')->default(false)->comment('Image principale');
      $table->timestamps();

      $table->index('property_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('images');
  }
};
