<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationPayment extends Model
{
    protected $table = 'allowance_payments';
    use HasFactory;
    protected $guarded = [];
    public function allowanceApplication()
    {
        $this->belongsTo(Allowance::class, 'allowance_application_id', 'id');
    }
}
