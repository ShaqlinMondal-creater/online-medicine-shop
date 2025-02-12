<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// use Illuminate\Database\Seeder;


use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '9876543210',
                'image' => 'users/admin.jpg',
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '9898989898',
                'image' => 'users/john.jpg',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '9797979797',
                'image' => 'users/jane.jpg',
            ],
            [
                'name' => 'Alex Brown',
                'email' => 'alex@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '9696969696',
                'image' => 'users/alex.jpg',
            ],
        ]);
    }
}

