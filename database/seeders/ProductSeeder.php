<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
   public function run(): void
{
    Product::truncate();

    $fruits = [
        ['name' => 'Mangue', 'price' => 400, 'weight' => 1.0, 'unit' => 'kg', 'img' => 'https://images.unsplash.com/photo-1553279768-865429fa0078'],
        ['name' => 'Orange', 'price' => 600, 'weight' => 1.0, 'unit' => 'kg', 'img' => 'https://images.unsplash.com/photo-1582979512210-99b6a53386f9'],
        ['name' => 'Citron Vert', 'price' => 1200, 'weight' => 5.0, 'unit' => 'sac', 'img' => 'https://images.unsplash.com/photo-1590505677574-1c3a6b834220'],
        ['name' => 'Papaye', 'price' => 800, 'weight' => 2.5, 'unit' => 'unité', 'img' => 'https://images.unsplash.com/photo-1517282009859-f000ec3b26fe'],
        ['name' => 'Banane', 'price' => 500, 'weight' => 1.0, 'unit' => 'kg', 'img' => 'https://images.unsplash.com/photo-1603833665858-e61d17a86224'],
    ];

    for ($i = 1; $i <= 20; $i++) {
        $fruitRef = $fruits[array_rand($fruits)];
        
        Product::create([
            'name'      => $fruitRef['name'] . " #" . $i,
            'price'     => $fruitRef['price'] + rand(-50, 100),
            'weight'    => $fruitRef['weight'], // On injecte le poids ici
            'unit'      => $fruitRef['unit'],
            'stock'     => rand(10, 100),
            'image_url' => $fruitRef['img'],
        ]);
    }
}
}