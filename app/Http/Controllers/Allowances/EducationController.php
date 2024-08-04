<?php

namespace App\Http\Controllers\Allowances;

use App\Exceptions\DuplicateApplicationNumberException;
use Throwable;
use App\Models\User;
use App\Models\Member;
use App\Models\Allowance;
use Illuminate\Http\Request;
use App\Models\WelfareScheme;
use Illuminate\Support\Carbon;
use App\Exports\AllowanceExport;
use App\Services\AllowanceService;
use Illuminate\Auth\Access\AuthorizationException;
use Maatwebsite\Excel\Facades\Excel;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class EducationController extends SmartController
{
    public function __construct(public  AllowanceService $allowanceService, Request $request) {
        parent::__construct($request);
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

    public function create()
    {
        $memberId = $this->request->input('member_id', null);
        $member = $memberId != null ? Member::with(['feePayments'])->where('id', $memberId)->get()->first() : null;
        $scheme = WelfareScheme::where('name', config('generalSettings.allowances')['education_assistance'])->get()->first();
        if (!$scheme->is_enabled) {
            throw new AuthorizationException("Unable to perform the action. Attempt to create application for a disabled scheme");
        }
        $schemeCode = $scheme->code;
        $today = Carbon::today()->format('d-m-Y');
        return $this->buildResponse(
            'admin.allowances.create',
            [
                'member' => $member,
                'scheme_code' => $schemeCode,
                'today' => $today
            ]);
    }

    public function edit($id)
    {
        $allowance = Allowance::find($id);
        $today = Carbon::today()->format('d-m-Y');
        if (!$allowance->editable_by_status) {
            return $this->buildResponse(
                'admin.error',
                [
                    'title' => 'Un-allowed action',
                    'message' => 'The application was already processed. Further editing not allowed.'
                ]
            );
        }

        return $this->buildResponse(
            'admin.allowances.edit',
            [
                'allowance' => $allowance,
                'today' => $today
            ]);
    }

    public function store()
    {
        try {
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
        } catch (DuplicateApplicationNumberException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    }

    public function update($id)
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
}
