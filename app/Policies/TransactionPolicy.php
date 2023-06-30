<?php

namespace App\Policies;

use App\User;
use App\Models\Accounting\Transaction;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user, $districtId)
    {
        if ($user->hasPermissionTo('transactions.view.any_district')) {
            return true;
        }
        if ($user->hasPermissionTo('transactions.view.own_district') && $user->district == $districtId) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  App\Models\Accounting\Transaction $transaction
     * @return mixed
     */
    public function view(User $user, Transaction $transaction)
    {
        if ($user->hasPermissionTo('transactions.view.any_district')) {
            return true;
        }
        if ($user->hasPermissionTo('transactions.view.own_district') && $user->district == $transaction->district_id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user, $districtId)
    {
        if ($user->hasPermissionTo('transactions.create.any_district')) {
            return true;
        } elseif ($user->hasPermissionTo('transactions.create.own_district') && $user->district == $districtId) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  App\Models\Accounting\Transaction $transaction
     * @return mixed
     */
    public function update(User $user, Transaction $transaction)
    {
        if ($user->hasPermissionTo('transactions.edit.any_district')) {
            return true;
        }
        if ($user->hasPermissionTo('transactions.edit.own_district') && $user->district == $transaction->district_id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  App\Models\Accounting\Transaction $transaction
     * @return mixed
     */
    public function delete(User $user, Transaction $transaction)
    {
        if ($user->hasPermissionTo('transactions.delete.any_district')) {
            return true;
        }
        if ($user->hasPermissionTo('transactions.delete.own_district') && $user->district == $transaction->district_id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  App\Models\Accounting\Transaction $transaction
     * @return mixed
     */
    public function restore(User $user, Transaction $transaction)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  App\Models\Accounting\Transaction $transaction
     * @return mixed
     */
    public function forceDelete(User $user, Transaction $transaction)
    {
        //
    }
}
