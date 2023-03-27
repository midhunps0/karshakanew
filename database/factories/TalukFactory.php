<?php

namespace Database\Factories;

use App\Models\District;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Taluk>
 */
class TalukFactory extends Factory
{
    private static $code = 0;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        Self::$code++;

        return [
            'display_code' => Self::$code,
            'district_id' => District::all()->random()->id,
            'name' => $this->faker->word() . ' Taluk',
            'enabled' => true,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ];
    }
}
