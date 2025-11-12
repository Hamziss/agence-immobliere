<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Create admin user
    User::create([
      'name' => 'Admin User',
      'email' => 'admin@digitup.com',
      'password' => Hash::make('password123'),
      'role' => 'admin',
    ]);

    // Create agent users
    User::create([
      'name' => 'Agent Mohammed',
      'email' => 'agent1@digitup.com',
      'password' => Hash::make('password123'),
      'role' => 'agent',
    ]);

    User::create([
      'name' => 'Agent Fatima',
      'email' => 'agent2@digitup.com',
      'password' => Hash::make('password123'),
      'role' => 'agent',
    ]);

    // Create guest user
    User::create([
      'name' => 'Guest User',
      'email' => 'guest@digitup.com',
      'password' => Hash::make('password123'),
      'role' => 'guest',
    ]);
  }
}
