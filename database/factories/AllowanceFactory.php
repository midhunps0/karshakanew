<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\WelfareScheme;
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
        $y = rand(1, 10);
        $sdays = rand(7, 14);
        $xdays = $sdays + rand(2, 5);
        return [
            'member_id' => Member::all()->random()->id,
            'application_no' => $this->faker->word().'/'.rand(101, 999),
            'application_date' => Carbon::now()->subYear($y),
            'applied_amount' => $amt,
            'sanctioned_amount' => $samt,
            'sanctioned_date' => Carbon::now()->subYear($y)->addDay($sdays),
            'payment_date' => Carbon::now()->subYear($y)->addDay($xdays),
            'welfare_scheme_id' => WelfareScheme::all()->random()->id
        ];
    }
}
