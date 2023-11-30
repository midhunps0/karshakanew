<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Accounting\LedgerAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

class LedgerAccountPolicy
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
        if ($user->hasPermissionTo('ledger_account.view.any_district')) {
            return true;
        }
        if ($user->hasPermissionTo('ledger_account.view.own_district') && $user->district_id == $districtId) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  App\Models\Accounting\LedgerAccount $account
     * @return mixed
     */
    public function view(User $user, LedgerAccount $account)
    {
        if ($user->hasPermissionTo('ledger_account.view.any_district')) {
            return true;
        }
        if ($user->hasPermissionTo('ledger_account.view.own_district') && $user->district_id == $account->district_id) {
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
        if ($user->hasPermissionTo('ledger_account.create.any_district')) {
            return true;
        } elseif ($user->hasPermissionTo('ledger_account.create.own_district') && $user->district_id == $districtId) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  App\Models\Accounting\LedgerAccount $account
     * @return mixed
     */
    public function store(User $user, $districtId)
    {
        if ($user->hasPermissionTo('Ledger Account: Create In Any District')) {
            return true;
        }
        if ($user->hasPermissionTo('Ledger Account: Create In Own District') && $user->district_id == $districtId) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  App\Models\Accounting\LedgerAccount $account
     * @return mixed
     */
    public function update(User $user, LedgerAccount $account)
    {
        if ($user->hasPermissionTo('ledger_account.edit.any_district')) {
            return true;
        }
        if ($user->hasPermissionTo('ledger_account.edit.own_district') && $user->district_id == $account->district_id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  App\Models\Accounting\LedgerAccount $account
     * @return mixed
     */
    public function delete(User $user, LedgerAccount $account)
    {
        if ($user->hasPermissionTo('ledger_account.delete.any_district')) {
            return true;
        }
        if ($user->hasPermissionTo('ledger_account.delete.own_district') && $user->district_id == $account->district_id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  App\Models\Accounting\LedgerAccount $account
     * @return mixed
     */
    public function restore(User $user, LedgerAccount $account)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  App\Models\Accounting\LedgerAccount $account
     * @return mixed
     */
    public function forceDelete(User $user, LedgerAccount $account)
    {
        //
    }
}
