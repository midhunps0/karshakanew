<?php

namespace App\Models;

use App\Models\AuditLog;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Allowance extends Model
{
    use HasFactory;

    static $STATUS_PENDING = 0;
    static $STATUS_APPROVED = 1;
    static $STATUS_REJECTED = -1;

    protected $with = [
        'welfareScheme'
    ];

    protected $casts = [
        'sanctioned_date' => 'datetime:d-m-Y',
    ];

    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function welfareScheme()
    {
        return $this->belongsTo(WelfareScheme::class, 'welfare_scheme_id', 'id');
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function allowanceable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $str = 'Pending';
                switch ($value) {
                    case self::$STATUS_APPROVED:
                        $str = 'Approved';
                        break;
                    case self::$STATUS_REJECTED:
                        $str = 'Rejected';
                        break;
                    default:
                        break;
                }
                return $str;
            },
        );
    }

    protected function sanctioned_date(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y'),
        );
    }
}
