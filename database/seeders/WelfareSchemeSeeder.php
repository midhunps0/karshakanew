<?php

namespace Database\Seeders;

use App\Models\WelfareScheme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WelfareSchemeSeeder extends Seeder
{
    private $schemes = [
        ['name' => 'Super Annuation', 'code' => 'SA'],
        ['name' => 'Maternity Assistance', 'code' => 'MTY'],
        ['name' => 'Marriage Assistance', 'code' => 'MRG'],
        ['name' => 'Death Ex-Gratia', 'code' => 'DEX'],
        ['name' => 'Super Annuation By Death', 'code' => 'DSA'],
        ['name' => 'Medical Assistance', 'code' => 'MED'],
        ['name' => 'Education Assistance', 'code' => 'EDU'],
        ['name' => 'Higher Education Assistance', 'code' => 'HEDU'],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // WelfareScheme::factory(10)->create();
        foreach ($this->schemes as $s) {
            WelfareScheme::factory()->create($s);
        }
    }
}
