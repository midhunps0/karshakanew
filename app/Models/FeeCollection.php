<?php

namespace App\Models;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Database\Eloquent\Builder;
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

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function feeItems()
    {
        return $this->hasMany(FeeItem::class, 'fee_collection_id', 'id');
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by', 'id');
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

    public function scopeUserDistrictConstrained(Builder $query)
    {
        $districtId = auth()->user()->district_id;
        if ($districtId != 15) {
            $query->where('district_id', $districtId);
        }
        return $query;
    }

    public function scopeUserConstrained(Builder $query)
    {
        /**
         * @var App\Models\User
         */
        $user = auth()->user();
        if ($user->hasPermissionTo('Fee Collection: View In Any District')) {
            return $query;
        } elseif($user->hasPermissionTo('Fee Collection: View Any In Own District')) {
            return $query->where('district_id', $user->district_id);
        } elseif('Fee Collection: View Own In Own District') {
            return $query->where('district_id', $user->district_id)
                ->where('collected_by', $user->id);
        }
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
