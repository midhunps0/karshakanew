<?php

namespace App\Models;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nominee extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    protected function dob(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return Carbon::parse($value)->format('d-m-Y');
            },
            set: function ($value) {
                $ch = '-';
                if (is_int(strpos($value, '/'))) {
                    $ch = '/';
                }
                $arr = explode($ch, $value);
                return $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            },
        );
    }
}
