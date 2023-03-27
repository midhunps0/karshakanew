<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\District>
 */
class DistrictFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->word();
        return [
            'display_code' => rand(1, 14),
            'name' => $name,
            'short_code' => Str::substr($name, 0, 3),
            'enabled' => true,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ];
    }
}
