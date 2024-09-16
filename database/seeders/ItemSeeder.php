<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = array(
            array('name' => 'butter', 'price' => 100),
            array('name' => 'cream', 'price' => 200),
            array('name' => 'water', 'price' => 300),
            array('name' => 'shoes', 'price' => 400),
            array('name' => 'shirt', 'price' => 500),
            array('name' => 'pant', 'price' => 600),
        );
        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
