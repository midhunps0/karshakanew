<?php

namespace Database\Factories;

use Closure;
use App\Models\User;
use App\Models\Member;
use App\Models\FeeItem;
use App\Models\FeeType;
use App\Models\PaymentMode;
use App\Models\FeeCollection;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeeCollection>
 */
class FeeCollectionFactory extends Factory
{
    private static $rno = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $m = Member::all()->random();
        $d = $m->district;
        Self::$rno++;
        $bookNo = 'FY23-24/'.$d->short_code;
        $receiptNo = $bookNo.'/'.Self::$rno;
        return [
            'member_id' => $m->id,
            'district_id' => $m->district_id,
            'book_number' => $bookNo,
            'receipt_number' => $receiptNo,
            'total_amount' => rand(100, 5000),
            'receipt_date' => $this->faker->date(),
            'payment_mode_id' => PaymentMode::all()->random()->id,
            'collected_by' => User::all()->random()->id,
            'notes' => $this->faker->sentence(),
            'manual_numbering' => false,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (FeeCollection $fc) {
            $n = rand(1, 3);
            $tot = $fc->total_amount;
            $amts = [];
            $sum = 0;

            if ($n > 1) {
                for ($i = 0; $i < $n; $i++) {
                    if ($i == ($n - 1)) {
                        $iamt = $tot - $sum;
                    } else {
                        $x = rand(90, 110);
                        $iamt = floor($tot / $n * $x /100);
                        $sum += $iamt;
                    }
                    $amts[] = $iamt;
                }
            } else {
                $amts[] = $tot;
            }

            for ($i = 0; $i < $n; $i++) {
                $ftId = FeeType::all()->random()->id;
                $data = [
                    'fee_collection_id' => $fc->id,
                    'fee_type_id' => $ftId,
                    'amount' => $amts[$i]
                ];
                if (!in_array($ftId, config('generalSettings.fee_types_with_tenure'))) {
                    $data['period_from'] = null;
                    $data['period_to'] = null;
                    $data['tenure'] = null;
                }
                FeeItem::factory()->create($data);
            }
        });
    }
}
