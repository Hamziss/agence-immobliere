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
    Schema::create('properties', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->enum('type', ['appartement', 'villa', 'terrain', 'bureau', 'local_commercial']);
      $table->integer('rooms')->nullable()->comment('Nombre de pièces');
      $table->decimal('surface', 10, 2)->comment('Surface en m²');
      $table->decimal('price', 15, 2);
      $table->string('city');
      $table->string('district')->nullable()->comment('Quartier/Commune');
      $table->text('description')->nullable();
      $table->enum('status', ['disponible', 'vendu', 'location'])->default('disponible');
      $table->boolean('is_published')->default(false);
      $table->string('title')->nullable()->comment('Titre généré automatiquement');
      $table->timestamps();
      $table->softDeletes();

      // Index pour optimiser les recherches
      $table->index(['city', 'type', 'status']);
      $table->index('is_published');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('properties');
  }
};
