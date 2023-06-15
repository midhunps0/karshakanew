<?php

namespace App\Http\Controllers;

use App\Models\Allowance;
use App\Models\Member;
use App\Models\WelfareScheme;
use Illuminate\Http\Request;
use App\Services\AllowanceService;
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

    public function show($id)
    {
        $application = Allowance::with(['allowanceable', 'welfareScheme', 'member'])->where('id', $id)->get()->first();
        return $this->buildResponse('admin.allowances.show', ['application' => $application]);
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
            $this->request->input('amount', null)
        );

        return response()->json($result);
    }
}
