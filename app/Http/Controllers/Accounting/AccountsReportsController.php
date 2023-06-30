<?php

namespace App\Http\Controllers\Accounting;

use App\Helpers\AccountsHelper;
use App\Http\Controllers\Controller;
use App\Services\AccountGroupService;
use Illuminate\Http\Request;

class AccountsReportsController extends Controller
{
    private $groupRepo;
    public function __construct(AccountGroupService $groupRepo)
    {
        $this->groupRepo = $groupRepo;
    }

    public function accountsChart()
    {
        $list = $this->groupRepo->accountsChart();
        return response()->json([
            'success'=>'true',
            'data' => $list
        ],200); ;
    }

    public function accountStatement(Request $request)
    {
        $request->validate([
            'account_id' => 'required|integer',
            'from' => 'required',
            'to' => 'required'
        ]);

        $ach = new AccountsHelper();
        $stmt = $ach->accountStatement(
            $request->input('account_id'),
            $request->input('from'),
            $request->input('to')
        );
        return response()->json([
            'success' => true,
            'data' => $stmt
        ]);
    }

    public function journalStatement(Request $request)
    {
        $request->validate([
            'district_id' => 'sometimes|integer',
            'type' => 'sometimes',
            'from' => 'required',
            'to' => 'required',
            'cashonly' => 'sometimes|boolean'
        ]);
        $districtId = $request->input('district_id') ?: auth()->user()->district;
        $ach = new AccountsHelper();
        $stmt = $ach->journalStatement(
            $districtId,
            $request->input('type'),
            $request->input('from'),
            $request->input('to'),
            $request->input('cashonly'),
        );
        return response()->json([
            'success' => true,
            'data' => $stmt
        ]);
    }

    public function transactionTypes()
    {
        return response()->json([
            'success' => true,
            'data' => config('accounts.transaction_types')
        ]);
    }
}
