<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'fee',
                'value' => 2,
            ],
        ];

        foreach ($items as $item) {
            Setting::create($item);
        }
    }
}
