<?php

namespace Database\Factories;

use App\Models\Caste;
use App\Models\Religion;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Religion>
 */
class ReligionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'enabled' => true,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ];
    }

    // public function configure(): static
    // {
    //     return $this->afterCreating(function (Religion $r) {
    //         $n = rand(3, 10);
    //         Caste::factory($n)->create([
    //             'religion_id' => $r->id
    //         ]);
    //     });
    // }
}
