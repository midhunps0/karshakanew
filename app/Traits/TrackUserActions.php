<?php
namespace App\Traits;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait TrackUserActions {

    protected static function booted(): void
    {
        static::created(function (Model $model) { // Convert Model to Trackable (interface)
            if (auth()->check()) {
                ActivityLog::create([
                    'user_id' => auth()->user()->id,
                    'model_type' => $model->get_class(),
                    'model_id' => $model->id,
                    'new_value' => $model->toJson(),
                    'description' => `Created $model->get_class(), id: $model->id` // Convert this to identifier string (using Trackable interdace's getIdentifier method)
                ]);
            }
        });

        static::updated(function (User $user) {
            // ...
        });

        static::deleted(function (User $user) {
            // ...
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by', 'id');
    }

    public function activityLogs()
    {
        return $this->morphMany(
            ActivityLog::class,
            'model',
        );
    }
}
?>
