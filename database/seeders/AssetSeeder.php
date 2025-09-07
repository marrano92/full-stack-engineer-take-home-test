<?php
declare( strict_types=1 );


namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder {

    public function run(): void
    {
        $owners = \App\Models\Owner::all();
        
        if ($owners->isEmpty()) {
            throw new \Exception('No owners found. Run OwnerSeeder first.');
        }

        \App\Models\Asset::factory(30)->create()->each(function ($asset) use ($owners) {
            $owner = $owners->random();
            $asset->update([
                'current_owner_id' => $owner->id,
                'current_owned_from' => fake()->dateTimeBetween('-2 years', 'now'),
            ]);
        });
    }
}
