<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class ReportsController extends SmartController
{
    public function snapshot(Request $request, ReportService $service)
    {
        $year_start = 2021;
        $now = Carbon::now();
        $year_now = $now->year;
        $month = $request->input('month') ?? $now->month;
        $year = $request->input('year') ?? $now->year;
        $districtId = $request->input('district');
        $years = [];
        for ($y = $year_start; $y <= $year_now; $y++) {
            $years[] = $y;
        }
        $districts = District::withoutHo()->get();
        $data = $service->snapshot($year, $month, $districtId);
        return $this->buildResponse(
            'admin.reports.snapshot',
            [
                'years' => $years,
                'year' => $year,
                'month' => $month,
                'collections' => $data['collections'],
                'allowances' => $data['allowances'],
                'districts' => $districts,
                'districtId' => $districtId
            ]
        );
    }
}
