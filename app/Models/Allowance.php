<?php

namespace App\Models;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
