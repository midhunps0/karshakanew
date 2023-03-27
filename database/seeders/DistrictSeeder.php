<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DistrictSeeder extends Seeder
{
    private $districts = [
        'Alappuzha',
        'Ernakulam',
        'Idukki',
        'Kannur',
        'Kasargode',
        'Kollam',
        'Kottayam',
        'Kozhikode',
        'Malappuram',
        'Palakkad',
        'Pathanamthitta',
        'Thrissur',
        'Trivandrum',
        'Wayanad',
        'Head Office'
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = 1;
        foreach($this->districts as $d) {
            District::create(
                [
                    'display_code' => $count,
                    'name' => $d,
                    'short_code' => Str::upper(substr($d, 0, 3)),
                    'enabled' => 1,
                ]
            );
            $count++;
        }
    }
}
