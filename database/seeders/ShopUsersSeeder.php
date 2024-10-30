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
            'admin',
            'Mohandessin Shop',
            'Mall of Arabia',
            'Nasr City',
            'Zamalek',
            'Mall of Egypt',
            'El Guezira Shop',
            'Arkan',
            'Online',
            'District 5',
            'U Venues',
            'rabea'
        ];

        foreach ($shops as $shop) {
            // $shopModel = Shop::where('name', $shop)->first();
            // if ($shopModel) {

            for($i=1 ; $i<= rand(3,4); $i++){
            User::create([
                'name' => "{$shop} {$i}",
                'email' => strtolower(str_replace(' ', '_', $shop)) . "{$i}@gmail.com",
                'password' => Hash::make('12345678'),
                'shop_name' =>$shop,
            ]);
        }
            // Update the shop_id to be the same as the user's id
        }
    }
}
