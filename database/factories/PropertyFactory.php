<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $types = ['appartement', 'villa', 'terrain', 'bureau', 'local_commercial'];
    $cities = ['Alger', 'Oran', 'Constantine', 'Annaba', 'Tlemcen', 'Blida'];
    $statuses = ['disponible', 'vendu', 'location'];
    $type = fake()->randomElement($types);

    return [
      'user_id' => User::factory(),
      'type' => $type,
      'rooms' => in_array($type, ['appartement', 'villa', 'bureau']) ? fake()->numberBetween(1, 8) : null,
      'surface' => fake()->randomFloat(2, 30, 500),
      'price' => fake()->randomFloat(2, 5000000, 100000000),
      'city' => fake()->randomElement($cities),
      'district' => fake()->optional()->city(),
      'description' => fake()->optional()->paragraph(),
      'status' => fake()->randomElement($statuses),
      'is_published' => fake()->boolean(70), // 70% chance of being published
    ];
  }

  /**
   * Indicate that the property is published.
   */
  public function published(): static
  {
    return $this->state(fn(array $attributes) => [
      'is_published' => true,
    ]);
  }

  /**
   * Indicate that the property is not published.
   */
  public function unpublished(): static
  {
    return $this->state(fn(array $attributes) => [
      'is_published' => false,
    ]);
  }
}
