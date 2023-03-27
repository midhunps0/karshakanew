<?php

namespace App\Models;

use App\Traits\TrackUserActions;
use Illuminate\Database\Eloquent\Model;
use Ynotz\EasyAdmin\Exceptions\ModelIntegrityViolationException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Village extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

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

    public function scopeInTaluk($query, $id)
    {
        return $query->where('taluk_id', $id);
    }
}
