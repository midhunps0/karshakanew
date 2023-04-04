<?php

namespace Database\Seeders;

use App\Models\Taluk;
use App\Models\Village;
use App\Models\District;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DistrictSeeder extends Seeder
{
    // private $districts = [
    //     'Alappuzha',
    //     'Ernakulam',
    //     'Idukki',
    //     'Kannur',
    //     'Kasargode',
    //     'Kollam',
    //     'Kottayam',
    //     'Kozhikode',
    //     'Malappuram',
    //     'Palakkad',
    //     'Pathanamthitta',
    //     'Thrissur',
    //     'Trivandrum',
    //     'Wayanad',
    //     'Head Office'
    // ];
    private $districts = [];
    private $taluks = [];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dopen = fopen("seeds/Districts.csv", "r");
        $count = 0;
        while (($data = fgetcsv($dopen, 1000, ",")) !== false) {
            if ($count > 0) {
                $d = District::create(
                    [
                        'display_code' => intval($data[1]),
                        'name' => $data[2],
                        'short_code' => $data[3],
                        'enabled' => 1,
                    ]
                );
                $data[] = $d->id;
                $this->districts[] = $data;
            }
            $count = 1;
        }

        $count = 0;
        $topen = fopen("seeds/taluks.csv", "r");

        while (($data = fgetcsv($topen, 1000, ",")) !== false) {
            if ($count > 0) {
                $t = Taluk::create(
                    [
                        'display_code' => intval($data[1]),
                        'name' => $data[3],
                        'district_id' => $this->getDistrict($data[2]),
                        'enabled' => 1,
                    ]
                );
                $data[] = $t->id;
                $this->taluks[] = $data;
            }
            $count = 1;
        }

        $count = 0;
        $vopen = fopen("seeds/taluks.csv", "r");
        while (($data = fgetcsv($vopen, 1000, ",")) !== false) {
            if ($count > 0) {
                Village::create(
                    [
                        'display_code' => intval($data[1]),
                        'name' => $data[2],
                        'taluk_id' => $this->getTaluk($data[2]),
                        'enabled' => 1,
                    ]
                );
            }
            $count = 1;
        }
        // foreach($this->districts as $d) {
        //     District::create(
        //         [
        //             'display_code' => $count,
        //             'name' => $d,
        //             'short_code' => Str::upper(substr($d, 0, 3)),
        //             'enabled' => 1,
        //         ]
        //     );
        //     $count++;
        // }
    }

    private function getDistrict($id)
    {
        $r =  array_filter($this->districts, function ($d) use ($id) {
            return $d[0] == $id;
        });
        return array_values($r)[0][4];
    }

    private function getTaluk($id)
    {
        // dd($this->taluks, $id);
        $r = array_filter($this->taluks, function ($t) use ($id) {
            return $t[0] == $id;
        });
        if (!isset(array_values($r)[0])) {
            dd($id, $r);
        }
        return array_values($r)[0][4];
    }
}
