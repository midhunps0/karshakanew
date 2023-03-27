<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    private $users = [
        [
            'name' => "System Admin",
            'email' => "admin@demo.com",
            'password' => '',
            'district_id' => 15,
            'role' => 'System Admin'
        ],
        [
            'name' => "State Admin",
            'email' => "stateadmin@demo.com",
            'password' => '',
            'district_id' => 15,
            'role' => 'State Admin'
        ],
        [
            'name' => "District Admin",
            'email' => "districtadmin@demo.com",
            'password' => '',
            'district_id' => 7,
            'role' => 'District Admin'
        ],
        [
            'name' => "State Executive",
            'email' => "stateexecutive@demo.com",
            'password' => '',
            'district_id' => 15,
            'role' => 'State Executive'
        ],
        [
            'name' => "District Executive",
            'email' => "districtexecutive@demo.com",
            'password' => '',
            'district_id' => 7,
            'role' => 'District Executive'
        ],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->users as $user) {
            $r = array_pop($user);
            $user['password'] = Hash::make('abcd1234');

            $u = User::create($user);
            $u->assignRole($r);
        }
    }
}
