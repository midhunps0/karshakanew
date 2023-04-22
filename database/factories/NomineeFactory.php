<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Nominee>
 */
class NomineeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $relations = ['Mother', 'Father', 'Son', 'Daughter', 'Wife', 'Husband'];
        $n = rand(0, 5);
        $g = rand(0, 1);
        $n = rand(0, 1);
        $d = Carbon::now()->subYears(rand(20, 50))->format('d-m-Y');
        // $x = $d->format('Y-m-d');
        return [
            'member_id' => Member::all()->random()->id,
            'name' => $this->faker->name(),
            'relation' => $relations[$n],
            'percentage' => rand(1, 99),
            'dob' => $d,
            'guardian_name' => $g == 1 ? $this->faker->name() : null,
            'guardian_relation' => $g == 1 ? $relations[$n] : null
        ];
    }
}
