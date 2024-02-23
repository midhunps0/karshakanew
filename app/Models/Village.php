<?php

namespace App\Models;

use App\Models\Taluk;
use App\Models\AuditLog;
use App\Traits\TrackUserActions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ynotz\EasyAdmin\Exceptions\ModelIntegrityViolationException;

class Village extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $appends = [
        'district'
    ];

    protected static function booted(): void
    {
        static::deleting(function (Village $village) {
            $tn = Village::selectRaw('COUNT(*) as count')->where('taluk_id', $village->id)->get()->first()->count;
            if ($tn > 0) {
                throw new ModelIntegrityViolationException("Cannot delete the taluk. This taluk has some associated Villages.");
            }
            $mn = Member::selectRaw('COUNT(*) as count')->where('taluk_id', $village->id)->get()->first()->count;
            if ($mn > 0) {
                throw new ModelIntegrityViolationException("Cannot delete the taluk. This taluk has some associated Members.");
            }
        });
    }

    public function taluk()
    {
        return $this->belongsTo(Taluk::class, 'taluk_id', 'id');
    }


    public function district(): Attribute
    {

        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $taluk = Taluk::find($this->taluk_id);
                return $taluk != null ? $taluk->district->name : '';
            }
        );
    }

    public function scopeInTaluk($query, $id)
    {
        return $query->where('taluk_id', $id);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function scopeUserAccessControlled(Builder $query)
    {
        $authUser = User::find(auth()->user()->id);
        $district = District::find($authUser->district_id);

        if (!$authUser->hasPermissionTo('Village: View In Any District')) {
            $query->whereIn('taluk_id', $district->taluks->pluck('id'));
        }
        return $query;
    }
}
