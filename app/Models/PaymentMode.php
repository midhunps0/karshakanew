<?php

namespace App\Models;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMode extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
