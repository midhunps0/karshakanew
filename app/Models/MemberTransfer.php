<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberTransfer extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    protected $with = [
        'member',
        'district',
        'taluk',
        'village'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function fromDistrict()
    {
        return $this->belongsTo(District::class, 'from_district_id', 'id');
    }

    public function taluk()
    {
        return $this->belongsTo(Taluk::class, 'taluk_id', 'id');
    }

    public function fromTaluk()
    {
        return $this->belongsTo(Taluk::class, 'from_taluk_id', 'id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id', 'id');
    }

    public function fromVillage()
    {
        return $this->belongsTo(Village::class, 'from_village_id', 'id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeRequestsPlaced($query)
    {
        $query->where('requestedby_id', auth()->user()->id);
    }

    public function scopeRequestsReceived($query)
    {
        $query->where('district_id', auth()->user()->district->id);
    }

    public function scopeRequestsApproved($query)
    {
        $query->where('processedby_id', auth()->user()->id);
    }
}
