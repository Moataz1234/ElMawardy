<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Shop;

class ShopUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shops = [
            'Mohandessin Office',
            'Mohandessin Shop',
            'Mall of Arabia',
            'Nasr City',
            'Zamalek',
            'Downtown',
            'Mall of Egypt',
            'El Guezira Shop',
            'El Korba',
            'Arkan',
            'Online',
            'Downtown2',
            'District 5',
            'U Venues',
        ];

        foreach ($shops as $shop) {
            $shopModel = Shop::where('name', $shop)->first();

            $user = User::create([
                'name' => $shop,
                'email' => strtolower(str_replace(' ', '_', $shop)) . '@example.com',
                'password' => Hash::make('12345678'),
            ]);

            // Update the shop_id to be the same as the user's id
            $user->shop_id = $user->id;
            $user->save();
        }
    }
}
