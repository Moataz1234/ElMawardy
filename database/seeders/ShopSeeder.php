<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shops')->insert([
            ['id' => 14, 'name' => 'Arkan', 'address' => null],
            ['id' => 18, 'name' => 'District 5', 'address' => null],
            ['id' => 9, 'name' => 'Downtown', 'address' => null],
            ['id' => 16, 'name' => 'Downtown2', 'address' => null],
            ['id' => 12, 'name' => 'El Guezira Shop', 'address' => null],
            ['id' => 13, 'name' => 'El Korba', 'address' => null],
            ['id' => 6, 'name' => 'Mall of Arabia', 'address' => null],
            ['id' => 11, 'name' => 'Mall of Egypt', 'address' => null],
            ['id' => 4, 'name' => 'Mohandessin Office', 'address' => null],
            ['id' => 5, 'name' => 'Mohandessin Shop', 'address' => null],
            ['id' => 7, 'name' => 'Nasr City', 'address' => null],
            ['id' => 15, 'name' => 'Online', 'address' => null],
            ['id' => 19, 'name' => 'U Venues', 'address' => null],
            ['id' => 8, 'name' => 'Zamalek', 'address' => null],
        ]);
    }
}
