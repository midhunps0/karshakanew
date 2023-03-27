<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function castes()
    {
        return $this->hasMany(Caste::class, 'religion_id', 'id');
    }
}
