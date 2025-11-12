<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $agents = User::where('role', 'agent')->get();

    if ($agents->isEmpty()) {
      $this->command->warn('No agents found. Please run UserSeeder first.');
      return;
    }

    $properties = [
      [
        'user_id' => $agents->first()->id,
        'type' => 'villa',
        'rooms' => 5,
        'surface' => 250,
        'price' => 45000000,
        'city' => 'Alger',
        'district' => 'Hydra',
        'description' => 'Belle villa moderne avec jardin et piscine, située dans un quartier calme et sécurisé.',
        'status' => 'disponible',
        'is_published' => true,
      ],
      [
        'user_id' => $agents->first()->id,
        'type' => 'appartement',
        'rooms' => 3,
        'surface' => 95,
        'price' => 12500000,
        'city' => 'Alger',
        'district' => 'Bab Ezzouar',
        'description' => 'Appartement F3 neuf avec toutes commodités, proche université et transports.',
        'status' => 'disponible',
        'is_published' => true,
      ],
      [
        'user_id' => $agents->last()->id,
        'type' => 'terrain',
        'rooms' => null,
        'surface' => 500,
        'price' => 25000000,
        'city' => 'Oran',
        'district' => 'Bir El Djir',
        'description' => 'Terrain constructible bien situé, titre foncier disponible.',
        'status' => 'disponible',
        'is_published' => true,
      ],
      [
        'user_id' => $agents->last()->id,
        'type' => 'bureau',
        'rooms' => 4,
        'surface' => 120,
        'price' => 8000000,
        'city' => 'Constantine',
        'district' => 'Centre-ville',
        'description' => 'Bureau spacieux idéal pour cabinet ou entreprise, bien équipé.',
        'status' => 'location',
        'is_published' => true,
      ],
      [
        'user_id' => $agents->first()->id,
        'type' => 'local_commercial',
        'rooms' => 2,
        'surface' => 80,
        'price' => 15000000,
        'city' => 'Alger',
        'district' => 'Didouche Mourad',
        'description' => 'Local commercial très bien placé sur artère principale.',
        'status' => 'disponible',
        'is_published' => true,
      ],
      [
        'user_id' => $agents->first()->id,
        'type' => 'appartement',
        'rooms' => 4,
        'surface' => 110,
        'price' => 18000000,
        'city' => 'Alger',
        'district' => 'Dely Ibrahim',
        'description' => 'Appartement F4 avec vue dégagée et balcon.',
        'status' => 'disponible',
        'is_published' => false,
      ],
      [
        'user_id' => $agents->last()->id,
        'type' => 'villa',
        'rooms' => 6,
        'surface' => 300,
        'price' => 55000000,
        'city' => 'Tlemcen',
        'district' => 'Imama',
        'description' => 'Villa de luxe avec architecture traditionnelle andalouse.',
        'status' => 'vendu',
        'is_published' => true,
      ],
    ];

    foreach ($properties as $property) {
      Property::create($property);
    }

    $this->command->info('Properties seeded successfully!');
  }
}
