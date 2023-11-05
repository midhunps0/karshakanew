<?php

namespace App\Models\Accounting;

use App\Models\District;
use Illuminate\Support\Carbon;
use App\Models\Accounting\AccountGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LedgerAccount extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'district_id',
        'name',
        'description',
        'group_id',
        'opening_balance',
        'opening_bal_type'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $appends = [
        'name_with_district'
    ];

    public function getOpeningBalanceAttribute($value)
    {
        return $value / 100;
    }
    public function setOpeningBalanceAttribute($value)
    {
        $this->attributes['opening_balance'] = $value * 100;
    }
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }
    public function group()
    {
        return $this->belongsTo(AccountGroup::class, 'group_id', 'id');
    }

    public function openingCreditBalance()
    {
        if ($this->opening_bal_type == 'credit') {
            return $this->opening_balance;
        }
        return $this->opening_balance * (-1);
    }

    public function scopeUserDistrictConstrained(Builder $query)
    {
        $districtId = auth()->user()->district_id;
        if ($districtId != 15) {
            $query->where('district_id', $districtId);
        }
        // else {
        //     $query->where('district_id', 2);
        // }
        return $query;
    }

    public function scopeIsCashOrBank(Builder $query) {
        $query->where('cashorbank', true);
    }

    public function scopeNotCashOrBank(Builder $query) {
        $query->where('cashorbank', false);
    }

    protected function nameWithDistrict(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => '('.$this->district->short_code.') '.$this->name,
        );
    }

    protected function iscashorbank(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value == 1 ? 'Yes' : 'No'
        );
    }
}
