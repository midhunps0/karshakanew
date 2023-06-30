<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Accounting\Transaction;
use App\User;
use App\Models\District;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(Transaction::class, function (Faker $faker) {
    return [
        'district_id' => District::all()->random()->id,
        'date' => Carbon::today(),
        'amount' => random_int(10000, 100000),
        'type' => array_rand(['payment', 'receipt', 'journal']),
        'ref_no' => '',
        'remarks' => $faker->sentence(3),
        'owner_id' => User::all()->random()->id
    ];
});
