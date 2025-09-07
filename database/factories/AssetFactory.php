<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
            'MacBook Pro 13"',
            'MacBook Pro 14"', 
            'MacBook Pro 15"',
            'MacBook Pro 16"',
            'MacBook Air 13"',
            'MacBook Air 15"',
            'iMac 21.5"',
            'iMac 24"',
            'iMac 27"',
            'iPad Pro 11"',
            'iPad Pro 12.9"',
            'iPad Air',
            'iPad Mini',
            'iPhone 15 Pro',
            'iPhone 15',
            'iPhone 16 Pro',
            'iPhone 16',
            'Smartphone Samsung S22',
            'Smartphone Samsung S23',
            'Smartphone Samsung S24',
            'Dell XPS 13',
            'Dell XPS 15',
            'ThinkPad X1 Carbon',
            'Surface Pro 9',
            'Surface Laptop 5',
            'Apple Watch Series 9',
            'AirPods Pro',
            'Monitor Dell 27"',
            'Monitor LG 32"',
            'Printer HP LaserJet'
        ];

        $serialPrefixes = ['AA', 'AB', 'CC', 'DD', 'EE', 'FF'];
        
        return [
            'reference' => fake()->randomElement($products),
            'serial_number' => fake()->randomElement($serialPrefixes) . fake()->numerify('######'),
            'description' => fake()->realText(fake()->numberBetween(50, 150)),
            'current_owner_id' => null, // Will be set by seeder
            'current_owned_from' => fake()->dateTimeBetween('-2 years', 'now'),
        ];
    }
}