<?php

namespace App\Models;

use App\Models\AuditLog;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Allowance extends Model
{
    use HasFactory;

    static $STATUS_PENDING = 0;
    static $STATUS_APPROVED = 1;
    static $STATUS_PAID = 2;
    static $STATUS_OLD_UNKNOWN = 3;
    static $STATUS_REJECTED = -1;

    protected $with = [
        'welfareScheme',
        'allowanceable',
        'member'
    ];

    // protected $casts = [
    //     'sanctioned_date' => 'datetime:d-m-Y',
    //     'appliaction_date' => 'datetime:d-m-Y',
    //     'payment_date' => 'datetime:d-m-Y',
    // ];

    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function welfareScheme()
    {
        return $this->belongsTo(WelfareScheme::class, 'welfare_scheme_id', 'id');
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function allowanceable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $str = 'None';
                switch ($value) {
                    case self::$STATUS_OLD_UNKNOWN:
                        $str = 'Old - Unknown';
                        break;
                    case self::$STATUS_PENDING:
                        $str = 'Pending';
                        break;
                    case self::$STATUS_PAID:
                        $str = 'Paid';
                        break;
                    case self::$STATUS_APPROVED:
                        $str = 'Approved';
                        break;
                    case self::$STATUS_REJECTED:
                        $str = 'Rejected';
                        break;
                    default:
                        break;
                }
                return $str;
            },
        );
    }

    protected function sanctionedDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value != null ? Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y') : '',
        );
    }

    protected function applicationDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value != null ? Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y') : '',
        );
    }

    protected function paymentDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value != null ? Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y') : '',
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
}
