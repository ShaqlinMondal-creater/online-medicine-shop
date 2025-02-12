<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::insert([
            [
                'name' => 'Paracetamol 500mg',
                'description' => 'Pain reliever and fever reducer.',
                'price' => 20.50,
                'stock' => 100,
                'category_id' => 1,
                'image' => 'products/paracetamol.jpg',
            ],
            [
                'name' => 'Ibuprofen 400mg',
                'description' => 'Anti-inflammatory and pain reliever.',
                'price' => 25.00,
                'stock' => 80,
                'category_id' => 1,
                'image' => 'products/ibuprofen.jpg',
            ],
            [
                'name' => 'Amoxicillin 250mg',
                'description' => 'Antibiotic used to treat bacterial infections.',
                'price' => 50.00,
                'stock' => 80,
                'category_id' => 2,
                'image' => 'products/amoxicillin.jpg',
            ],
            [
                'name' => 'Azithromycin 500mg',
                'description' => 'Antibiotic for bacterial infections.',
                'price' => 60.00,
                'stock' => 50,
                'category_id' => 2,
                'image' => 'products/azithromycin.jpg',
            ],
            [
                'name' => 'Vitamin C 1000mg',
                'description' => 'Boosts immunity and skin health.',
                'price' => 30.00,
                'stock' => 120,
                'category_id' => 3,
                'image' => 'products/vitamin_c.jpg',
            ],
            [
                'name' => 'Vitamin D3 5000 IU',
                'description' => 'Supports bone and immune health.',
                'price' => 40.00,
                'stock' => 100,
                'category_id' => 3,
                'image' => 'products/vitamin_d3.jpg',
            ],
            [
                'name' => 'Cough Syrup',
                'description' => 'Relieves cough and sore throat.',
                'price' => 35.00,
                'stock' => 90,
                'category_id' => 4,
                'image' => 'products/cough_syrup.jpg',
            ],
            [
                'name' => 'Antihistamine Tablets',
                'description' => 'Treats allergies and cold symptoms.',
                'price' => 20.00,
                'stock' => 110,
                'category_id' => 4,
                'image' => 'products/antihistamine.jpg',
            ],
            [
                'name' => 'Insulin Injection',
                'description' => 'Controls blood sugar levels in diabetics.',
                'price' => 150.00,
                'stock' => 50,
                'category_id' => 5,
                'image' => 'products/insulin.jpg',
            ],
            [
                'name' => 'Metformin 500mg',
                'description' => 'Helps control blood sugar in diabetics.',
                'price' => 45.00,
                'stock' => 70,
                'category_id' => 5,
                'image' => 'products/metformin.jpg',
            ],
            [
                'name' => 'Acne Cream',
                'description' => 'Treats acne and pimples.',
                'price' => 60.00,
                'stock' => 40,
                'category_id' => 6,
                'image' => 'products/acne_cream.jpg',
            ],
            [
                'name' => 'Sunscreen SPF 50',
                'description' => 'Protects skin from harmful UV rays.',
                'price' => 75.00,
                'stock' => 90,
                'category_id' => 6,
                'image' => 'products/sunscreen.jpg',
            ],
            [
                'name' => 'Aspirin 81mg',
                'description' => 'Reduces the risk of heart attacks.',
                'price' => 15.00,
                'stock' => 100,
                'category_id' => 7,
                'image' => 'products/aspirin.jpg',
            ],
            [
                'name' => 'Losartan 50mg',
                'description' => 'Lowers blood pressure.',
                'price' => 55.00,
                'stock' => 60,
                'category_id' => 7,
                'image' => 'products/losartan.jpg',
            ],
            [
                'name' => 'Omega-3 Fish Oil',
                'description' => 'Supports heart and brain health.',
                'price' => 80.00,
                'stock' => 75,
                'category_id' => 3,
                'image' => 'products/omega3.jpg',
            ],
        ]);
    }
}
