<?php

namespace App\Services;

use Illuminate\Support\Facades\Gate;
use App\Models\Accounting\AccountGroup;
use App\Models\Accounting\LedgerAccount;
use Illuminate\Validation\UnauthorizedException;

class AccountGroupService
{
    public function findOrFail($id)
    {
        $group =  AccountGroup::findOrFail($id);
        // if (Gate::denies('view', [$group])) {
        //     throw new UnauthorizedException('You are not authorised to view this resource', 401);
        // }
        return $group;
    }

    public function update($input, $id, $attribute = 'id')
    {
        /*
        $result = parent::update($input, $id);
        $group = null;
        if ($result) {
            $group = $this->find($id);
        }
        return $group;
        */
    }

    public function accountsChart()
    {
        return AccountGroup::with(['subGroupsFamilyAccounts'])->where('parent_id', null)
            ->userDistrictConstrained()
            ->get();
    }
}
