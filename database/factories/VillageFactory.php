<?php

namespace Database\Factories;

use App\Models\Taluk;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Village>
 */
class VillageFactory extends Factory
{
    private static $code;
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
            'taluk_id' => Taluk::all()->random()->id,
            'name' => $this->faker->word() . ' Village',
            'enabled' => true,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ];
    }
}
