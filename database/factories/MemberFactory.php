<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Member;
use App\Models\District;
use App\Models\Nominee;
use App\Models\Religion;
use App\Models\TradeUnion;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    private $genders = [
        'Male',
        'Female',
        'Others'
    ];

    private $relationships = [
        'Father',
        'Mother',
        'Spouse'
    ];
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $dn = rand(1, 14);
        $d = District::find($dn);
        $t = $d->taluks->random();
        $v = $t->villages->random();
        $mno = $d->display_code . '/'
                . $t->display_code . '/'
                . $v->display_code . '/' .rand(1, 9999);
        $y = rand(30, 50);
        $dob = Carbon::now()->subYear($y)->format('Y-m-d');
        $r = Religion::all()->random();
        $c = $r->castes->random();
        return [
            'name' => $this->faker->name(),
            'district_id' => $d->id,
            'district_office_id' => $d->id,
            'district_residing_id' => $d->id,
            'taluk_id' => $t->id,
            'village_id' => $v->id,
            'membership_no' => $mno,
            'aadhaar_no' => rand(1234123412341234, 9876987698769876),
            'mobile_no' => rand(6543210987, 9876543210),
            'gender' => $this->genders[rand(0, 2)],
            'dob' => $dob,
            'parent_guardian' => $this->faker->name(),
            'guardian_relationship' => $this->relationships[rand(0, 2)],
            'marital_status' => '',
            'current_address' => $this->faker->address(),
            'permanent_address' => $this->faker->address(),
            'identification_mark_a' => $this->faker->sentence(5),
            'identification_mark_b' => $this->faker->sentence(5),
            'religion_id' => $r->id,
            'caste_id' => $c->id,
            'trade_union_id' => TradeUnion::all()->random()->id,
            // 'verified' => true,
            'active' => true,
            'created_by' => User::all()->random()->id,
            'approved_by' => null,
            'approved_at' => null,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(
            function (Member $m) {
                $n = rand(1, 3);
                $sum = 100;
                $remaining = $n;
                for ($i = 0; $i < $n; $i++) {
                    if ($remaining == 1) {
                        $p = $sum;
                    } else {
                        $p = rand(1, $sum - 1);
                    }
                    Nominee::factory()->create(
                        [
                            'member_id' => $m->id,
                            'percentage' => $p
                        ]
                    );
                    $sum = $sum - $p;
                    $remaining = $n - 1;
                }
            }
        );
    }
}
