<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeeCollection extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $with = [
        'feeItems'
    ];

    protected $appends = [
        'formatted_receipt_date',
        'is_editable_period'
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

    protected function formattedReceiptDate(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $tdate = new \DateTime($this->receipt_date);
                return $tdate->format('d-m-Y');
            },
        );
    }

    protected function isEditablePeriod(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $now = new \DateTime();
                $today = $now->format('d-m-Y');
                $ctime = new \DateTime($this->created_at);
                $cdate =$ctime->format('d-m-Y');
                return $today == $cdate;
            },
        );
    }
}
