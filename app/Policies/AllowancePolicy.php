<?php

namespace App\Policies;

use App\Models\Allowance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AllowancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Allowance $allowance): bool
    {
        return $user->hasPermissionTo('Allowance: View In Any District') ||
            (($user->hasPermissionTo('Allowance: View In Own District') &&
                $user->district_id == $allowance->district_id));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Allowance $allowance): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Allowance $allowance): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Allowance $allowance): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Allowance $allowance): bool
    {
        //
    }

    /**
     * Determine whether the user can approve an allowance application
     */
    public function approve(User $user, Allowance $allowance): bool
    {
        return $user->hasPermissionTo('Allowance: Approve In Any District') ||
            ($user->hasPermissionTo('Allowance: Approve In Own District') &&
                $user->district_id == $allowance->district_id);
    }

    /**
     * Determine whether the user can approve an allowance application
     */
    public function viewReport(User $user): bool
    {
        return $user->hasPermissionTo('Allowance: View Report In Any District') ||
            $user->hasPermissionTo('Allowance: View Report In Own District');
    }
}
