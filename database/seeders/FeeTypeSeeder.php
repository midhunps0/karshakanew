<?php

namespace Database\Seeders;

use App\Models\FeeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeeTypeSeeder extends Seeder
{
    private $types = [
        'Annual Subscription',
        'Cost Of Passbook',
        'Fine',
        'Arrears'
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->types as $type) {
            FeeType::factory()->create([
                'name' => $type
            ]);
        }
    }
}
