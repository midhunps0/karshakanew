<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\User;
use App\Models\Member;
use App\Models\Allowance;
use Illuminate\Http\Request;
use App\Models\WelfareScheme;
use App\Services\AllowanceService;
use Maatwebsite\Excel\Facades\Excel;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class AllowanceController extends SmartController
{
    public function __construct(public  AllowanceService $allowanceService, Request $request){
        parent::__construct($request);
    }

    public function index()
    {
        return $this->buildResponse('admin.allowances.index');
    }

    public function pending()
    {
        $result = $this->allowanceService->pending();
        return $this->buildResponse(
            'admin.allowances.pending',
            [
                'allowances' => $result
            ]
        );
    }

    public function report()
    {
        $start = $this->request->input('start', null);
        $end = $this->request->input('end', null);
        $page = $this->request->input('page', 1);

        try {
            if (count($this->request->all()) == 0) {
                $result = [];
            } else {
                $result = $this->allowanceService->report([
                    'start' => $start,
                    'end' => $end,
                    'page' => $page,
                    'datetype' => $this->request->input('datetype', 'receipt_date'),
                    'created_by' => $this->request->input('created_by', null),
                    'status' => $this->request->input('status', null)
                ]);
            }
            $user = User::find(auth()->user()->id);
            $appUsers = User::userAccessControlled()->get();
            return $this->buildResponse('admin.allowances.report', ['allowances' => $result, 'appUsers' => $appUsers, 'user' => $user]);
        } catch (Throwable $e) {
            info($e);
            dd($e);
            return $this->buildResponse('admin.allowances.report', [
                'allowances' => [],
                'error' => 'Sorry, an unexpected error occured. Unable to fetch the report.'
            ]);
        }
    }

    public function fullReport()
    {
        $start = $this->request->input('start', null);
        $end = $this->request->input('end', null);
        $result = [];

        if ($start != null || $end != null) {
            $result = $this->allowanceService->report([
                'start' => $start,
                'end' => $end,
                'fullreport' => true,
                'datetype' => $this->request->input('datetype', 'receipt_date'),
                'datetype' => $this->request->input('datetype', 'receipt_date'),
                'status' => $this->request->input('status', null)
            ]);
        }
        return response()->json([
            'receipts' => $result
        ]);
    }

    public function download(Excel $excel)
    {
        $start = $this->request->input('start', null);
        $end = $this->request->input('end', null);
        $page = $this->request->input('page', 1);
            $result = [];
        $result = collect([]);
        // if ($start != null || $end != null) {
            $result = $this->allowanceService->report(
                [
                    'start' => $start,
                    'end' => $end,
                    'fullreport' => true,
                    'datetype' => $this->request->input('datetype', 'receipt_date'),
                    'status' => $this->request->input('status', null)
                ]
            );
        // }

        return Excel::download(new AllowanceExport($result), 'receipts.csv');
    }

    public function show($id)
    {
        $application = Allowance::with(['allowanceable', 'welfareScheme', 'member'])->where('id', $id)->get()->first();
        if (auth()->user()->can('view', $application)) {
            return $this->buildResponse('admin.allowances.show', ['application' => $application]);
        } else {
            return $this->buildResponse('admin.allowances.show', ['error' => 'You are not authorised to view this receipt.', 'application' => null]);
        }
    }

    public function educationCreate()
    {
        $memberId = $this->request->input('member_id', null);
        $member = $memberId != null ? Member::with(['feePayments'])->where('id', $memberId)->get()->first() : null;
        $schemeCode = WelfareScheme::where('name', 'Education Assistance')->get()->first()->code;
        return $this->buildResponse(
            'admin.allowances.create',
            [
                'member' => $member,
                'scheme_code' => $schemeCode
            ]);
    }

    public function educationStore()
    {
        $result = $this->allowanceService->storeEducationSchemeApplication($this->request->all());
        return response()->json([
            'application' => $result
        ]);
    }

    public function approve($id)
    {
        $result = $this->allowanceService->approve(
            $id,
            $this->request->input('approval'),
            $this->request->input('amount', null),
            $this->request->input('rejection_reason', null),
        );

        return response()->json($result);
    }
}
