<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeCollection extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $with = [
        'feeItems'
    ];

    protected $casts = [
        'period_from' => 'datetime:d-m-Y',
        'period_to' => 'datetime:d-m-Y',
    ];

    public function feeItems()
    {
        return $this->hasMany(FeeItem::class, 'fee_collection_id', 'id');
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by_id', 'id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_id', 'id');
    }
}
