<?php

namespace App\Http\Controllers;

use App\Models\Allowance;
use App\Models\District;
use App\Models\WelfareScheme;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdhocController extends Controller
{
    private $schemes = [
        'Education Assistance',
        'Death Ex-Gratia',
        'Marriage Assistance',
        'Maternity Assistance',
        'Medical Assistance',
        'Super Annuation',
        'Higher Education Assistance',
    ];
    public function seedLastAllowanceApplNos()
    {
        $data = [];
        foreach (District::where('id', '<', 15)->get() as $d) {
            info("------- District: $d->name -------------");
            foreach ($this->schemes as $s) {
                $scheme = WelfareScheme::where('name', $s)->get()->first();
                info("Scheme: $scheme->name ($scheme->id), District: $d->name ($d->id)");
                $sCount = Allowance::where('application_date', '>', Carbon::createFromFormat('d-m-Y', '01-04-2024')
                    ->startOfDay()->format('Y-m-d'))
                    ->where('welfare_scheme_id', $scheme->id)
                    ->where('district_id', $d->id)
                    ->count();
                info("Count: $sCount");
                $data[$s] = $sCount;
            }
            $d->last_appl_no_json = json_encode($data);
            $d->save();
        }

        return 'ok';
    }

    public function applicationNosCorrection(Request $request)
    {
        $year = $request->input('year', 2024);
        $year = intval($year);
        $date = Carbon::today();
        $date->setYear($year);
        $fyStart = $date->setMonth(Carbon::APRIL)->startOfMonth();
        $endYear = $year + 1;
        $fyEnd = Carbon::today()
            ->setYear($endYear)
            ->setMonth(Carbon::APRIL)
            ->startOfMonth();
        $districtIds = District::where('id', '<', 15)->get()->pluck('id');
        $schemeIds = WelfareScheme::all()->pluck('id');

        $lastNoData = [];

        foreach ($districtIds as $d) {
            foreach ($schemeIds as $sid) {
                $lastNoData[$d][$sid] = 0;
            }
        }

        $fyAllowances = Allowance::where('application_date', '>=', $fyStart->format('Y-m-d H:i:s'))
            ->where('application_date', '<', $fyEnd->format('Y-m-d H:i:s'))
            ->orderBy('')
            ->get();

        $count = 0;
        foreach ($fyAllowances as $a) {
            $appNoArr = explode('/', $a->application_no);
            array_pop($appNoArr);

            $appNo = implode('/', $appNoArr).'/'.($lastNoData[$a->district_id][$a->welfare_scheme_id] + 1);

            $a->application_no = $appNo;
            $a->save();

            $lastNoData[$a->district_id][$a->welfare_scheme_id] += 1;

            $count++;
        }

        return "Updated $count application numbers.";
    }
}
