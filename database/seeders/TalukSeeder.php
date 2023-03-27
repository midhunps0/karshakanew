<?php

namespace Database\Seeders;

use App\Models\Taluk;
use App\Models\District;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TalukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (District::all() as $d) {
            $n = rand(7, 8);
            for ($i=0; $i < $n; $i++) {
                Taluk::factory()->create([
                    'district_id' => $d->id
                ]);
            }
        }
    }
}
