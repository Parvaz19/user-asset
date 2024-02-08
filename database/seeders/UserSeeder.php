<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'ali',
                'email' => 'ali@example.com',
                'password' => '123456'
            ],
            [
                'name' => 'test',
                'email' => 'test@example.com',
                'password' => '123456'
            ]
        ];

        foreach($items as $item){
            User::create($item);
        }

    }
}
