<?php

namespace Database\Seeders;

use App\Models\Caste;
use App\Models\Religion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CasteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Religion::all() as $r) {
            $n = rand(3, 10);
            for ($i=0; $i < $n; $i++) {
                Caste::factory()->create([
                    'religion_id' => $r->id
                ]);
            }
        }
    }
}
