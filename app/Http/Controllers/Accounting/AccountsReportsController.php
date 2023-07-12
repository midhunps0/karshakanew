<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\AccountsHelper;
use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\Accounting\LedgerAccount;
use App\Services\AccountGroupService;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class AccountsReportsController extends SmartController
{
    private $groupRepo;
    public function __construct(Request $request, AccountGroupService $groupRepo)
    {
        $this->request = $request;
        $this->groupRepo = $groupRepo;
    }

    public function accountsChart()
    {
        $list = $this->groupRepo->accountsChart();
        return $this->buildResponse('admin.accounts.reports.chart_of_accounts', [
            'accounts' => $list,
        ]);
    }

    public function accountStatement(Request $request)
    {
        // $request->validate([
        //     'account_id' => 'required|integer',
        // ]);
        $allAccounts = LedgerAccount::userDistrictConstrained()->get();
        $ach = new AccountsHelper();
        $stmt = null;
        $account = null;
        $from = $request->input('from', Carbon::today()->startOfMonth()->format('d-m-Y'));
        $to = $request->input('to', Carbon::today()->format('d-m-Y'));
        if ($request->input('account_id', null) != null) {
            $stmt = $ach->accountStatement(
                $request->input('account_id'),
                $from,
                $to,
            );
            $account = LedgerAccount::find($request->input('account_id'));
        }
        return $this->buildResponse('admin.accounts.reports.account_statement', [
            'statement' => $stmt,
            'account' => $account,
            'allAccounts' => $allAccounts,
            'from' => $from,
            'to' => $to
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
