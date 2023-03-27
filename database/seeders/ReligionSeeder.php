<?php

namespace Database\Seeders;

use App\Models\Religion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReligionSeeder extends Seeder
{
    private $religions = [
        'Hindu',
        'Muslim',
        'Christian',
        'Sikh',
        'Budhist',
        'Jain'
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->religions as $religion) {
            Religion::create([
                'name' => $religion
            ]);
        }
    }
}
