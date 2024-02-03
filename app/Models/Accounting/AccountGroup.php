<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use App\Models\Accounting\LedgerAccount;
use App\Models\District;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'district_id',
        'name',
        'parent_id',
        'is_core_group'
    ];
    public function accounts()
    {
        return $this->hasMany(LedgerAccount::class, 'group_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function parentGroup()
    {
        return $this->belongsTo(AccountGroup::class, 'parent_id', 'id');
    }

    public function subGroups()
    {
        return $this->hasMany(AccountGroup::class, 'parent_id', 'id');
    }

    public function subGroupsFamily()
    {
        return $this->hasMany(AccountGroup::class, 'parent_id', 'id')->with('subGroups');
    }

    public function subGroupsFamilyAccounts()
    {
        return $this->hasMany(AccountGroup::class, 'parent_id', 'id')->with(['subGroups', 'accounts']);
    }

    public function hasParent()
    {
        return isset($this->parentGroup);
    }

    // public function scope(Type $args): void
    // {
    //     # code...
    // }

    public function getParentChain()
    {
        $parents = [];
        $parent = $this->parentGroup;
        while(isset($parent)) {
            $parents[] = $parent;
            $parent = $parent->parentGroup;
        }
        return $parents;
    }

    public function rootParent()
    {
        if(!isset($this->parentGroup)){
            return $this;
        }
        $parent = $this->parentGroup;
        while(isset($parent)) {
            if(!isset($parent->parentGroup)) {
                return $parent;
            }
            $parent = $parent->parentGroup;
        }
    }

    public function isEditAllowed()
    {
        return !$this->is_core_group;
    }

    public function scopeUserDistrictConstrained(Builder $query, $districtId = null)
    {
        $districtId = $districtId ?? auth()->user()->district_id;
        $query->where('district_id', $districtId);
        return $query;
    }
}
