<?php

namespace App\Models;

use App\Models\Taluk;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ynotz\EasyAdmin\Exceptions\ModelIntegrityViolationException;

class District extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::deleting(function (District $district) {
            $tn = Taluk::selectRaw('COUNT(*) as count')->where('district_id', $district->id)->get()->first()->count;
            if ($tn > 0) {
                throw new ModelIntegrityViolationException("Cannot delete the district. This district has some associated Taluks.");
            }
            $mn = Member::selectRaw('COUNT(*) as count')->where('district_id', $district->id)->get()->first()->count;
            if ($mn > 0) {
                throw new ModelIntegrityViolationException("Cannot delete the district. This district has some associated Members.");
            }
        });
    }

    public function taluks()
    {
        return $this->hasMany(Taluk::class, 'district_id', 'id');
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'district_id', 'id');
    }

    public function residingMembers()
    {
        return $this->hasMany(Member::class, 'district_residing_id', 'id');
    }

    public function officeMembers()
    {
        return $this->hasMany(Member::class, 'district_office_id', 'id');
    }

    public function scopeUserAccessControlled(Builder $query)
    {
        $authUser = User::find(auth()->user()->id);
        if (!$authUser->hasPermissionTo('Member: View In Any District')) {
            $query->where('id', $authUser->district_id);
        }
        return $query;
    }

    public function scopeWithoutHo(Builder $query)
    {
        $query->where('id', '<>', 15);
        return $query;
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
