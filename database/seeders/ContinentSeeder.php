<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContinentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $continents = [
            ['name' => 'Africa', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Antarctica', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Asia', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Europe', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'North America', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Australia', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'South America', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('continents')->insert($continents);
    }
}
