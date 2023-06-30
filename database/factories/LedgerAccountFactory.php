<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Accounting\AccountGroup;
use App\Models\Accounting\LedgerAccount;
use App\Models\District;
use Faker\Generator as Faker;

$factory->define(LedgerAccount::class, function (Faker $faker) {
    return [
        'district_id' => District::all()->random()->id,
        'name' => $faker->sentence(3),
        'description' => $faker->sentence(5),
        'group_id' => AccountGroup::all()->random()->id,
        'opening_balance' => 0,
        'opening_bal_type' => 'credit',
    ];
});
