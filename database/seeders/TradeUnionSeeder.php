<?php

namespace Database\Seeders;

use App\Models\TradeUnion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TradeUnionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TradeUnion::factory(12)->create();
    }
}
