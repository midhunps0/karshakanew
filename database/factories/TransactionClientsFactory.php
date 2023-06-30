<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Accounting\LedgerAccount;
use App\Models\Accounting\TransactionClient;
use Faker\Generator as Faker;
use App\Models\Accounting\Transaction;

$factory->define(TransactionClient::class, function (Faker $faker) {
    return [
        'transaction_id' => Transaction::all()->random()->id,
        'ledger_account_id' => LedgerAccount::all()->random()->id,
        'client_amount' => $faker->numberBetween(1000, 100000),
        'action' => array_rand(['credit', 'debit'])
    ];
});
