<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'khakha@example.com',
            'password' => Hash::make('password'),
        ]);
        $makers = [
            ['code' => 1, 'name' => 'Honda'],
            ['code' => 2, 'name' => 'Yamaha'],
            ['code' => 3, 'name' => 'Suzuki'],
        ];

        DB::table('maker')->insert($makers);

        // Dữ liệu mẫu cho models
        $models = [
            ['name' => 'Wave Alpha', 'maker_code' => 1],
            ['name' => 'Air Blade', 'maker_code' => 1],
            ['name' => 'Exciter', 'maker_code' => 2],
            ['name' => 'Sirius', 'maker_code' => 2],
            ['name' => 'Raider', 'maker_code' => 3],
        ];

        DB::table('model')->insert($models);
    }
}
