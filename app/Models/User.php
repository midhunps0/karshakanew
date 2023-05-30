<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\AuditLog;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Ynotz\MediaManager\Traits\OwnsMedia;
use Ynotz\AccessControl\Traits\WithRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, WithRoles, OwnsMedia;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id', 'id');
    }

    public function scopeUserAccessControlled(Builder $query)
    {
        $authUser = User::find(auth()->user()->id);
        if (!$authUser->hasPermissionTo('User: View In Any District')) {
            $query->where('district_id', $authUser->district_id);
        }
        return $query;
    }

    public function scopeExceptSelf(Builder $query)
    {
        $query->where('id', '<>', auth()->user()->id);
        return $query;
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
