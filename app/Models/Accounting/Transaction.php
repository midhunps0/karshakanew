<?php

namespace App\Models\Accounting;

use App\User;
use App\Models\District;
use App\Helpers\AppHelper;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accounting\TransactionClient;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $appends = [
        'formatted_date'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getAmountAttribute($value)
    {
        return $value / 100;
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function clients()
    {
        return $this->hasMany(TransactionClient::class, 'transaction_id');
    }

    public function debtors()
    {
        return $this->hasMany(TransactionClient::class, 'transaction_id')
            ->where('action', '=', 'debit');
    }

    public function creditors()
    {
        return $this->hasMany(TransactionClient::class, 'transaction_id')
            ->where('action', '=', 'credit');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function scopeFromDate($query, $date)
    {
        $fd = AppHelper::dateFromString($date);
        return $query->where('date', '>=', $fd);
    }

    public function scopeToDate($query, $date)
    {
        $td = AppHelper::dateFromString($date)->addDay();
        return $query->where('date', '<', $td);
    }

    protected function formattedDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->date != null ? Carbon::createFromFormat('Y-m-d', $this->date)->format('d-m-Y') : '',
        );
    }
    // public function getOpeningBalance($accountId, $date)
    // {
    //     $dDate = AppHelper::dateFromString($date);
    //     $credits = Transaction::from('transactions as t')
    //         ->join('transaction_clients as tc', 't.id', '=', 'tc.transaction_id')
    //         ->where('tc.');
    //         //remove account alias concept, include account grouping
    //     $debits = 0;
    //     $bal = $credits - $debits;
    //     return $bal;
    // }
}
