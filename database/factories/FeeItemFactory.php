<?php

namespace Database\Factories;

use App\Models\FeeType;
use App\Models\FeeCollection;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeeItem>
 */
class FeeItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $yrsFrom = rand(4, 10);
        $k = rand(1, 3);
        $yrsTo = $yrsFrom - $k;
        /**
         * @var Datetime
         */
        $period_from = $this->faker->dateTimeInInterval('-' . $yrsFrom . ' years', '+2 days');
        $period_from = Carbon::parse($period_from)->startOfMonth()
            ->subMonthsNoOverflow();
        $fdate = $period_from->format('d-m-Y');
        /**
         * @var Datetime
         */
        $period_to = $this->faker->dateTimeInInterval('-'. $yrsTo . ' years', '+2 days');
        $period_to = Carbon::parse($period_to)->subMonthsNoOverflow()
            ->endOfMonth();
        $tdate = $period_to->format('d-m-Y');
        return [
            'fee_collection_id' => 1,
            'fee_type_id' => 1,
            'period_from' => $period_from->format('Y-m-d'),
            'period_to' => $period_to->format('Y-m-d'),
            'tenure' => $fdate . ' to ' . $tdate,
            'amount' => rand(10000, 500000)/100,
        ];
    }
}
