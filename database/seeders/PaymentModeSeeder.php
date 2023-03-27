<?php

namespace Database\Seeders;

use App\Models\PaymentMode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentModeSeeder extends Seeder
{
    private $modes = [
        'Cash',
        'Razorpay'
    ];

    public function run(): void
    {
        foreach ($this->modes as $m) {
            PaymentMode::factory()->create(
                [
                    'name' => $m
                ]
            );
        }
    }
}
