<?php

namespace App\Http\Controllers\Allowances;

use Throwable;
use App\Models\User;
use App\Models\Member;
use App\Models\Allowance;
use Illuminate\Http\Request;
use App\Models\WelfareScheme;
use Illuminate\Support\Carbon;
use App\Exports\AllowanceExport;
use App\Services\AllowanceService;
use Maatwebsite\Excel\Facades\Excel;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class AllowanceController extends SmartController
{
    public function __construct(public  AllowanceService $allowanceService, Request $request) {
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
                    'status' => $this->request->input('status', null),
                    'scheme' => $this->request->input('scheme', null),
                    'course' => $this->request->input('course', null),
                ]);
            }
            $user = User::find(auth()->user()->id);
            $appUsers = User::userAccessControlled()->get();
            $schemes = WelfareScheme::select(['id', 'name', 'code'])->get();
            return $this->buildResponse('admin.allowances.report', ['allowances' => $result, 'appUsers' => $appUsers, 'user' => $user, 'cols' => $this->request->input('cls'), 'schemes' => $schemes]);
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
                'status' => $this->request->input('status', null),
                'scheme' => $this->request->input('scheme', null),
                'course' => $this->request->input('course', null),
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

        if (count($this->request->all()) == 0) {
            $result = [];
        } else {
            $result = $this->allowanceService->report([
                'start' => $start,
                'end' => $end,
                'page' => $page,
                'datetype' => $this->request->input('datetype', 'receipt_date'),
                'created_by' => $this->request->input('created_by', null),
                'status' => $this->request->input('status', null),
                'fullreport' => true
            ]);
        }
        $formattedResults = [];

        foreach ($result as $item) {
            $temp = [];
            $temp['application_date'] = $item->application_date;
            $temp['application_no'] = $item->application_no;
            $temp['member_name'] = $item->member->name;
            $temp['membership_no'] = $item->member->membership_no;
            $temp['scheme_name'] = $item->welfareScheme->name;
            $temp['status'] = $item->status;
            $temp['sanctioned_amount'] = $item->sanctioned_amount;
            $temp['sanctioned_date'] = $item->sanctioned_date;
            $temp['payee_name'] = $item->allowanceable != null ? $item->allowanceable->member_bank_account['bank_name'] : '';
            $temp['bank_branch'] = $item->allowanceable != null ? $item->allowanceable->member_bank_account['bank_branch'] : '';
            $temp['account_no'] = $item->allowanceable != null ? "'" . strval($item->allowanceable->member_bank_account['account_no']) : '';
            $temp['ifsc_code'] = $item->allowanceable != null ? $item->allowanceable->member_bank_account['ifsc_code'] : '';
            $temp['payment_date'] = $item->payment_date;
            $temp['created_by'] = $item->createdBy->name;
            $temp['district'] = $item->district->name;

            if (isset($item->allowanceable->passed_exam_details)) {
                $temp['course'] =isset($item->allowanceable) ? $item->allowanceable->passed_exam_details['exam_name'] : '';
            }

            $formattedResults[] = $temp;
        }
        $allColumns = [
            0 => ['application_date' => 'Application Date'],
            1 => ['application_no' => 'Application No.'],
            2 => ['member_name' => 'Member Name'],
            3 => ['membership_no' => 'Membership NoApplication Date'],
            4 => ['scheme_name' => 'Scheme Name'],
            5 => ['status' => 'Status'],
            6 => ['sanctioned_amount' => 'Sanctioned Amount'],
            7 => ['sanctioned_date' => 'Sanctioned Date'],
            8 => ['payee_name' => 'Payee Name'],
            9 => ['bank_branch' => 'Bank & Branch'],
            10 => ['account_no' => 'Account No.'],
            11 => ['ifsc_code' => 'IFSC Code'],
            12 => ['payment_date' => 'Payment Date'],
            13 => ['created_by' => 'Created By'],
            14 => ['district' => 'District'],
            101 => ['course' => 'Course'],
        ];
        $selectedCols = [];
        foreach (explode('|', $this->request->input('cls')) as $c) {
            foreach ($allColumns[intval($c)] as $k => $v) {
                $selectedCols[$k] = $v;
            }
            // $selectedCols[] = $allColumns[intval($c)];
        }
        return Excel::download(new AllowanceExport(
            ['results' => $formattedResults, 'columns' => $selectedCols]
        ), 'allowances.csv');
    }

    // public function show($id)
    // {
    //     $application = Allowance::with(['allowanceable', 'welfareScheme', 'member'])->where('id', $id)->get()->first();
    //     if (auth()->user()->can('view', $application)) {
    //         return $this->buildResponse('admin.allowances.show', ['application' => $application]);
    //     } else {
    //         return $this->buildResponse('admin.allowances.show', ['error' => 'You are not authorised to view this receipt.', 'application' => null]);
    //     }
    // }
/*
    public function educationCreate()
    {
        $memberId = $this->request->input('member_id', null);
        $member = $memberId != null ? Member::with(['feePayments'])->where('id', $memberId)->get()->first() : null;
        $schemeCode = WelfareScheme::where('name', 'Education Assistance')->get()->first()->code;
        $today = Carbon::today()->format('d-m-Y');
        return $this->buildResponse(
            'admin.allowances.create',
            [
                'member' => $member,
                'scheme_code' => $schemeCode,
                'today' => $today
            ]);
    }

    public function educationEdit($id)
    {
        $allowance = Allowance::find($id);
        $today = Carbon::today()->format('d-m-Y');
        return $this->buildResponse(
            'admin.allowances.edit',
            [
                'allowance' => $allowance,
                'today' => $today
            ]);
    }

    public function educationStore()
    {
        $result = $this->allowanceService->storeEducationSchemeApplication($this->request->all());
        if ($result == false) {
            return response()->json([
                'success' => false,
                'message' => 'An application is already submitted for this student register number.'
            ]);
        }
        return response()->json([
            'success' => true,
            'application' => $result
        ]);
    }

    public function educationUpdate($id)
    {
        $result = $this->allowanceService->updateEducationSchemeApplication($id, $this->request->all());
        if ($result == false) {
            return response()->json([
                'success' => false,
                'message' => 'An application is already submitted for this student register number.'
            ]);
        }
        return response()->json([
            'success' => true,
            'application' => $result
        ]);
    }
*/
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
