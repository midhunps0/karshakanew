<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeeItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'formatted_period_from',
        'formatted_period_to',
    ];

    protected $with = ['feeType'];

    public function feeType()
    {
        return $this->belongsTo(FeeType::class, 'fee_type_id', 'id');
    }

    protected function formattedPeriodFrom(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $tdate = new \DateTime($this->period_from);
                return $tdate->format('d-m-Y');
            },
        );
    }

    protected function formattedPeriodTo(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $tdate = new \DateTime($this->period_to);
                return $tdate->format('d-m-Y');
            },
        );
    }
}
