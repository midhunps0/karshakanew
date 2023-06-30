<?php

namespace App\Models\Accounting;

use App\Helpers\AppHelper;
use App\Models\Accounting\Transaction;
use App\Models\Accounting\AccountAlias;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accounting\LedgerAccount;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionClient extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;
    protected $fillable = [
        'transaction_id',
        'ledger_account_id',
        'client_amount',
        'action'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $appends = [
        'ledger_account_name'
    ];
    protected $with = [
        'ledgerAccount'
    ];

    public function getClientAmountAttribute($value)
    {
        return $value / 100;
    }

    public function setClientAmountAttribute($value)
    {
        $this->attributes['client_amount'] = $value * 100;
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function ledgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class, 'ledger_account_id', 'id');
    }

    public function getLedgerAccountNameAttribute()
    {
        return $this->ledgerAccount->name;
    }

    public function scopeType($query, $type)
    {
        return $query->where('action', '=', $type);
    }

    public function scopeFromDate($query, $from)
    {
        $fromDate = AppHelper::dateFromString($from);
        return $query->with('transaction')
            ->whereHas('transaction', function ($query) use ($fromDate) {
                $query->where('date','>=',$fromDate);
            });
    }

    public function scopeToDate($query, $to)
    {
        $toDate = AppHelper::dateFromString($to)->addDay();
        return $query->with('transaction')
            ->whereHas('transaction', function ($query) use ($toDate) {
                $query->where('date','<',$toDate);
            });
    }

    public function scopeForAccounts($query, array $accountIds)
    {
        return $query->whereIn('ledger_account_id', $accountIds);
    }
}
