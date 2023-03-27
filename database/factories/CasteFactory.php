<?php

namespace Database\Factories;

use App\Models\Religion;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Caste>
 */
class CasteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'religion_id' => Religion::all()->random()->id,
            'name' => $this->faker->word(). ' Caste',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ];
    }
}
