<?php

namespace App\Policies;

use App\User;
use App\Models\Accounting\AccountGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountGroupPolicy
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
        if ($user->hasPermissionTo('account_group.view.any_district')) {
            return true;
        }
        if ($user->hasPermissionTo('account_group.view.own_district') && $user->district == $districtId) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  App\Models\Accounting\AccountGroup $group
     * @return mixed
     */
    public function view(User $user, AccountGroup $group)
    {
        if ($user->hasPermissionTo('account_group.view.any_district')) {
            return true;
        }
        if ($user->hasPermissionTo('account_group.view.own_district') && $user->district == $group->district_id) {
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
        if ($user->hasPermissionTo('account_group.create.any_district')) {
            return true;
        } elseif ($user->hasPermissionTo('account_group.create.own_district') && $user->district == $districtId) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  App\Models\Accounting\AccountGroup $group
     * @return mixed
     */
    public function update(User $user, AccountGroup $group)
    {
        if ($user->hasPermissionTo('account_group.edit.any_district')) {
            return true;
        }
        if ($user->hasPermissionTo('account_group.edit.own_district') && $user->district == $group->district_id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  App\Models\Accounting\AccountGroup $group
     * @return mixed
     */
    public function delete(User $user, AccountGroup $group)
    {
        if ($user->hasPermissionTo('account_group.delete.any_district')) {
            return true;
        }
        if ($user->hasPermissionTo('account_group.delete.own_district') && $user->district == $group->district_id) {
            return true;
        }
        return false;
    }
}
