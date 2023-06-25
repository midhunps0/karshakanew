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
                $tdate = $this->period_from != null ? new \DateTime($this->period_from) : null;
                return $tdate != null ? $tdate->format('d-m-Y') : null;
            },
        );
    }

    protected function formattedPeriodTo(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $tdate = $this->period_to != null ? new \DateTime($this->period_to) : null;
                return $tdate != null ? $tdate->format('d-m-Y') : null;
            },
        );
    }
}
