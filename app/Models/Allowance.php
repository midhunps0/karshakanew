<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    use HasFactory;

    protected $with = [
        'welfareScheme'
    ];

    protected $guarded = [];

    public function welfareScheme()
    {
        return $this->belongsTo(WelfareScheme::class, 'welfare_scheme_id', 'id');
    }
}
