<?php

namespace App\Models;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ynotz\EasyAdmin\Exceptions\ModelIntegrityViolationException;

class Taluk extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::deleting(function (Taluk $taluk) {
            $tn = Village::selectRaw('COUNT(*) as count')->where('taluk_id', $taluk->id)->get()->first()->count;
            if ($tn > 0) {
                throw new ModelIntegrityViolationException("Cannot delete the taluk. This taluk has some associated Villages.");
            }
            $mn = Member::selectRaw('COUNT(*) as count')->where('taluk_id', $taluk->id)->get()->first()->count;
            if ($mn > 0) {
                throw new ModelIntegrityViolationException("Cannot delete the taluk. This taluk has some associated Members.");
            }
        });
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function villages()
    {
        return $this->hasMany(Village::class, 'taluk_id', 'id');
    }

    public function scopeInDistrict($query, $id)
    {
        return $query->where('district_id', $id);
    }

    public function scopeUserAccessControlled(Builder $query)
    {
        $authUser = User::find(auth()->user()->id);
        if (!$authUser->hasPermissionTo('Taluk: View In Any District')) {
            $query->where('district_id', $authUser->district_id);
        }
        return $query;
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
