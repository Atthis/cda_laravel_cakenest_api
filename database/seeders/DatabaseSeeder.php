<?php

namespace Database\Seeders;

use App\Models\Cupcake;
use App\Models\Purchase;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $users = User::factory()->count(15)->create();

        $cupcakes = Cupcake::factory()->count(30)->create();

        // $purchases = Purchase::factory()->count(5)->create(['user_id' => $users->random(1)->pluck('id')]);
        $purchases = [];

        for ($index=0; $index < 5; $index++) {
            array_push($purchases, Purchase::factory()->create(['user_id' => $users->random(1)->pluck('id')[0]]));
        }

        foreach($purchases as $purchase) {
            $cupcakes_id = $cupcakes->random(rand(1, 8))->pluck('id')->toArray();

            foreach($cupcakes_id as $cupcake_id) {
                $purchase->cupcakes()->attach(
                    $cupcake_id,
                    [
                        'quantity' => rand(1,3),
                        'price' => Cupcake::find($cupcake_id)->price_in_cents
                    ]
                );
            }

        }
    }
}
