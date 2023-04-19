<?php

namespace Database\Factories;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Allowance>
 */
class AllowanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amt = rand(1000, 20000);
        $samt = $amt * rand(80, 100) / 100;
        $y = rand(10);
        return [
            'membership_no' => Member::all()->random()->id,
            'application_no' => $this->faker->word().'/'.rand(101, 999),
            'application_date' => Carbon::now()->subYear($y),
            'applied_amount' => $amt,
            'sanctioned_amount' => $samt,
            'payment_date' => Carbon::now()->subYear($y)->addDay(25)
        ];
    }
}
