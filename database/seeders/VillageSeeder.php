<?php

namespace Database\Seeders;

use App\Models\Taluk;
use App\Models\Village;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Taluk::all() as $t) {
            $n = rand(6, 9);
            for ($i=0; $i < $n; $i++) {
                Village::factory()->create([
                    'taluk_id' => $t->id
                ]);
            }
        }
    }
}
