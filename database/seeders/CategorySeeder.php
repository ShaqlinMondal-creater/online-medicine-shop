<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::insert([
            ['name' => 'Painkillers', 'icon' => 'icons/painkillers.png'],
            ['name' => 'Antibiotics', 'icon' => 'icons/antibiotics.png'],
            ['name' => 'Vitamins & Supplements', 'icon' => 'icons/vitamins.png'],
            ['name' => 'Cold & Flu', 'icon' => 'icons/cold_flu.png'],
            ['name' => 'Diabetes', 'icon' => 'icons/diabetes.png'],
            ['name' => 'Skin Care', 'icon' => 'icons/skin_care.png'],
            ['name' => 'Heart & Blood Pressure', 'icon' => 'icons/heart.png'],
        ]);
    }
}



// icon png inside the public folder