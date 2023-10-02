<?php

namespace App\Models;

use App\Models\User;
use App\Models\Nominee;
use App\Models\AuditLog;
use App\Models\District;
use App\Traits\TrackUserActions;
use Illuminate\Database\Eloquent\Model;
use Ynotz\MediaManager\Traits\OwnsMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class Member extends Model
{
    use HasFactory, SoftDeletes, OwnsMedia;

    // protected $casts = [
    //     'dob' => 'datetime:d-m-Y',
    //     'work_start_date' => 'datetime:d-m-Y',
    //     'created_by' => 'datetime:d-m-Y',
    //     'approved_at' => 'datetime:d-m-Y',
    //     'created_at' => 'datetime:d-m-Y',
    //     'updated_at' => 'datetime:d-m-Y',
    //     'deleted_at' => 'datetime:d-m-Y',
    //     'reg_date' => 'datetime:d-m-Y',
    // ];

    protected $guarded = [];

    protected $appends = [
        'photo',
        'application_front',
        'application_back',
        'aadhaar_card',
        'bank_passbook',
        'ration_card',
        'wb_passbook_front',
        'wb_passbook_back',
        'one_and_same_cert',
        'other_doc',
        'is_approved',
        'display_name',
        'display_current_address',
        'is_age_over'
    ];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function residingDistrict()
    {
        return $this->belongsTo(District::class, 'district_residing_id', 'id');
    }

    public function districtOffice()
    {
        return $this->belongsTo(District::class, 'district_office_id', 'id');
    }

    public function taluk()
    {
        return $this->belongsTo(Taluk::class, 'taluk_id', 'id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id', 'id');
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class, 'religion_id', 'id');
    }

    public function caste()
    {
        return $this->belongsTo(Casre::class, 'caste_id', 'id');
    }

    public function tradeUnion()
    {
        return $this->belongsTo(TradeUnion::class, 'trade_union_id', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function feePayments()
    {
        return $this->hasMany(FeeCollection::class, 'member_id', 'id');
    }

    public function lastFeePaidPeriod()
    {
        $membership_fee_id = config('generalSettings.membership_fee_id', 1);
        $lastPaidFrom = null;
        $lastPaidTo = null;
        foreach ($this->feePayments as $fp) {
            foreach ($fp->feeItems as $fi) {
                if ($fi->fee_type_id == $membership_fee_id) {
                    $df = $fi->period_from != null ? Carbon::createFromFormat('Y-m-d', $fi->period_from) : null;
                    $dt = $fi->period_to != null ? Carbon::createFromFormat('Y-m-d', $fi->period_to) : null;
                    if ($df != null && ($lastPaidFrom == null || $df->gt($lastPaidFrom))) {
                        $lastPaidFrom = $df;
                    }
                    if ($dt != null && ($lastPaidTo == null || $dt->gt($lastPaidTo))) {
                        $lastPaidTo = $dt;
                    }
                }
            }

        }
        $today = Carbon::today();
        info('reg_date');
        info($this->reg_date);
        $tillDate = $lastPaidTo != null ? $lastPaidTo : Carbon::createFromFormat('d-m-Y', $this->reg_date);
        return [
            'from' => $lastPaidFrom != null ? $lastPaidFrom->format('d-m-Y') : null,
            'to' => $lastPaidTo != null ? $lastPaidTo->format('d-m-Y') : null,
            'arrears_months' => $today->diffInMonths($tillDate)
        ];
    }

    public function feeItems()
    {
        return $this->hasManyThrough(
            FeeItem::class,
            FeeCollection::class,
            'member_id',
            'fee_collection_id',
            'id',
            'id'
        );
    }

    public function nominees()
    {
        return $this->hasMany(Nominee::class, 'member_id', 'id');
    }

    public function allowances()
    {
        return $this->hasMany(Allowance::class, 'member_id', 'id');
    }

    public function transferRequest()
    {
        return $this->hasOne(MemberTransfer::class, 'member_id', 'id');
    }

    public function isTransferPending(): Attribute
    {
        return Attribute::make(
            get: function ($val) {
                return $this->transferRequest != null &&
                    $this->transferRequest->processedby_id == null;
            }
        );
    }

    public function scopeUserAccessControlled(Builder $query)
    {
        $authUser = User::find(auth()->user()->id);
        if (!$authUser->hasPermissionTo('Member: View In Any District')) {
            $query->where('district_id', $authUser->district_id);
        }
        return $query;
    }

    public function scopeApproved(Builder $query)
    {
        return $query->where('approved_at', '<>', null);
    }

    public function scopeUnapproved(Builder $query)
    {
        return $query->where('approved_at', null);
    }

    public function getMediaStorage(): array
    {
        return [
            'photo'=> [
                'disk' => 's3',
                'folder' => 'public/images/photo'
            ],
            'application_front'=> [
                'disk' => 's3',
                'folder' => 'public/images/application_front'
            ],
            'application_back'=> [
                'disk' => 's3',
                'folder' => 'public/images/application_back'
            ],
            'aadhaar_card'=> [
                'disk' => 's3',
                'folder' => 'public/images/aadhaar_card'
            ],
            'bank_passbook' => [
                'disk' => 's3',
                'folder' => 'public/images/bank_passbook'
            ],
            'ration_card' => [
                'disk' => 's3',
                'folder' => 'public/images/ration_card'
            ],
            'wb_passbook_front' => [
                'disk' => 's3',
                'folder' => 'public/images/wb_passbook_front'
            ],
            'wb_passbook_back' => [
                'disk' => 's3',
                'folder' => 'public/images/wb_passbook_back'
            ],
            'one_and_same_cert' => [
                'disk' => 's3',
                'folder' => 'public/images/one_and_same_cert'
            ],
            'other_doc' => [
                'disk' => 's3',
                'folder' => 'public/images/other_doc'
            ],
        ];
    }

    protected function isAgeOver(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if ($this->dob == null) {
                    return false;
                }
                $today = Carbon::today();
                $theDob = Carbon::createFromFormat('d-m-Y', $this->dob);
                return $today->diffInYears($theDob) > 60;
            },
        );
    }

    protected function isApproved(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->approved_at != null;
            },
        );
    }

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('photo');
            },
        );
    }

    protected function applicationFront(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('application_front');
            },
        );
    }

    protected function applicationBack(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('application_back');
            },
        );
    }

    protected function aadhaarCard(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('aadhaar_card');
            },
        );
    }

    protected function bankPassbook(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('bank_passbook');
            },
        );
    }

    protected function rationCard(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('ration_card');
            },
        );
    }

    protected function wbPassbookFront(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('wb_passbook_front');
            },
        );
    }

    protected function wbPassbookBack(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('wb_passbook_back');
            },
        );
    }

    protected function oneAndSameCert(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('one_and_same_cert');
            },
        );
    }

    protected function otherDoc(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('other_doc');
            },
        );
    }

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if ($this->name != null && $this->name != '') {
                    if ($this->name_mal != null && $this->name_mal != '') {
                        return $this->name . '(' .$this->name_mal .')';
                    }
                    return $this->name;
                }
                return $this->name_mal ?? '';
            },
        );
    }

    protected function displayCurrentAddress(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if ($this->current_address != null && $this->current_address != '') {
                    if ($this->current_address_mal != null && $this->current_address_mal != '') {
                        return $this->current_address . '(' .$this->current_address_mal .')';
                    }
                    return $this->current_address;
                }
                return $this->current_address_mal ?? '';
            },
        );
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    protected function dob(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (Carbon::createFromFormat('Y-m-d', $value))->format('d-m-Y'),
        );
    }

    protected function regDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y'),
        );
    }

    protected function approvedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value != null ? Carbon::createFromTimestamp($value)->format('d-m-Y') : null,
        );
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::createFromTimestamp($value)->format('d-m-Y'),
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::createFromTimestamp($value)->format('d-m-Y'),
        );
    }
}
