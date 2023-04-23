<?php

namespace App\Models;

use App\Models\User;
use App\Models\Nominee;
use App\Models\District;
use App\Traits\TrackUserActions;
use Illuminate\Database\Eloquent\Model;
use Ynotz\MediaManager\Traits\OwnsMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory, SoftDeletes, OwnsMedia;

    protected $casts = [
        'dob' => 'datetime:d-m-Y',
        'work_start_date' => 'datetime:d-m-Y',
        'created_by' => 'datetime:d-m-Y',
        'approved_at' => 'datetime:d-m-Y',
        'created_at' => 'datetime:d-m-Y',
        'updated_at' => 'datetime:d-m-Y',
        'deleted_at' => 'datetime:d-m-Y',
    ];

    protected $guarded = [];

    protected $appends = [
        'aadhaar_card',
        'bank_passbook',
        'ration_card',
        'wb_passbook_front',
        'wb_passbook_back',
        'one_and_same_cert'
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

    public function scopeUserAccessControlled(Builder $query)
    {
        $authUser = User::find(auth()->user()->id);
        if (!$authUser->hasPermissionTo('User: View In Any District')) {
            $query->where('district_id', $authUser->district_id);
        }
        return $query;
    }

    public function getMediaStorage(): array
    {
        return [
            'aadhaarCard'=> [
                'disk' => 'local',
                'folder' => 'public/images/aadhaar_card'
            ],
            'bankPassbook' => [
                'disk' => 'local',
                'folder' => 'public/images/bank_passbook'
            ],
            'wbPassbookFront' => [
                'disk' => 'local',
                'folder' => 'public/images/wb_passbook_front'
            ],
            'wbPassbookBack' => [
                'disk' => 'local',
                'folder' => 'public/images/wb_passbook_back'
            ],
            'oneAndSameCert' => [
                'disk' => 'local',
                'folder' => 'public/images/one_and_same_cert'
            ],
        ];
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
}
