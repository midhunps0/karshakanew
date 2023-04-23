<?php

namespace Database\Seeders;

use App\Models\WelfareScheme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WelfareSchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WelfareScheme::factory(10)->create();
    }
}
