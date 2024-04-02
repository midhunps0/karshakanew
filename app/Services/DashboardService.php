<?php
namespace App\Services;

use App\Helpers\AppHelper;
use App\Models\Allowance;
use App\Models\District;
use App\Models\FeeType;
use App\Models\WelfareScheme;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function dashboardData($from, $to)
    {
		$from = AppHelper::formatDateForSave($from);
        $to = AppHelper::formatDateForSave($to);
        $result = null;
        $data = [];
        $level = null;
        $feeTypes = FeeType::all()->pluck('name')->toArray();
        $ditricts = null;
        $theTaluks = null;
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();
		//dd(collect($user->permissions())->pluck('name'));
		//dd($user->hasPermissionTo('Dashboard: View All District Data'));
        if ($user->hasPermissionTo('Dashboard: View All District Data')) {
		//if (in_array('Dashboard: View All District Data',collect($user->permissions())->pluck('name'))) {
			//dd('okay1');
            $level = 'state';
            $districts = District::withoutHo()->orderBy('display_code', 'asc')->get()->pluck('name')->toArray();
            foreach ($districts as $d) {
                $data[$d] = [];
            }
            $data['Total'] = [];
            $result = DB::table('fee_collections', 'fc')
                ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
                ->join('fee_types as ft', 'ft.id', '=', 'fi.fee_type_id')
                ->join('districts as d', 'd.id', '=', 'fc.district_id')
                ->select('d.name as district', 'ft.name as fee_type', DB::raw('SUM(fi.amount) as amount'))
                ->where('fc.receipt_date', '>=', $from)
                ->where('fc.receipt_date', '<=', $to)
                ->groupBy('ft.id', 'fc.district_id','d.id')
                ->orderBy('d.display_code', 'asc')
                ->get();
                foreach ($result as $r) {
                    $data[$r->district][$r->fee_type] = $r->amount;
                    $data['Total'][$r->fee_type] = $data['Total'][$r->fee_type] ?? 0;
                    $data['Total'][$r->fee_type] += $r->amount;
                    $data[$r->district]['Total'] = $data[$r->district]['Total'] ?? 0;
                    $data[$r->district]['Total'] += $r->amount;
                    $data['Total']['Total'] = $data['Total']['Total'] ?? 0;
                    $data['Total']['Total'] += $r->amount;
                }
        } elseif ($user->hasPermissionTo('Dashboard: View Own District Data')) {
			//dd('okay');
            $level = 'district';
            $d = District::find($user->district_id);
            $theTaluks = $d->taluks->pluck('name')->toArray();
            foreach ($theTaluks as $t) {
                $data[$t] = [];
            }
            $data['Total'] = [];
            $result = DB::table('fee_collections', 'fc')
                ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
                ->join('fee_types as ft', 'ft.id', '=', 'fi.fee_type_id')
                ->join('members as m', 'm.id', '=', 'fc.member_id')
                ->join('taluks as t', 't.id', '=', 'm.taluk_id')
                ->where('fc.district_id', $user->district_id)
                ->where('fc.receipt_date', '>=', $from)
                ->where('fc.receipt_date', '<=', $to)
                ->select('t.name as taluk', 'ft.name as fee_type', DB::raw('SUM(fi.amount) as amount'))
                ->groupBy('ft.id', 'm.taluk_id','t.id')
                ->orderBy('t.display_code', 'desc')
                ->get();
                foreach ($result as $r) {
                    $data[$r->taluk][$r->fee_type] = $r->amount;
                    $data['Total'][$r->fee_type] = $data['Total'][$r->fee_type] ?? 0;
                    $data['Total'][$r->fee_type] += $r->amount;
                    $data[$r->taluk]['Total'] = $data[$r->taluk]['Total'] ?? 0;
                    $data[$r->taluk]['Total'] += $r->amount;
                    $data['Total']['Total'] = $data['Total']['Total'] ?? 0;
                    $data['Total']['Total'] += $r->amount;
                }
        }
        // $response = [
        //     'level' => $level,
        //     'data' => $data,
        // ];
        if ($level == 'state') {
            return [
                'success' => true,
                'level' => $level,
                'data' => $data,
                'districts' => $districts,
                'fee_types' => $feeTypes
            ];
        } elseif ($level == 'district') {
            $taluks = $theTaluks;
            return [
                'success' => true,
                'level' => $level,
                'data' => $data,
                'taluks' => $taluks,
                'fee_types' => $feeTypes
            ];
        }
    }
    public function dashboardAllowancesData($from, $to)
    {
		$from = AppHelper::formatDateForSave($from);
        $to = AppHelper::formatDateForSave($to);
        $result = null;
        $data = [];
        $level = null;
        $allowanceTypes = WelfareScheme::all()->pluck('name')->toArray();
        $ditricts = null;
        $theTaluks = null;
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();
		//dd(collect($user->permissions())->pluck('name'));
		//dd($user->hasPermissionTo('Dashboard: View All District Data'));
        if ($user->hasPermissionTo('Dashboard: View All District Data')) {
		//if (in_array('Dashboard: View All District Data',collect($user->permissions())->pluck('name'))) {
			//dd('okay1');
            $level = 'state';
            $districts = District::withoutHo()->orderBy('display_code', 'asc')->get()->pluck('short_code')->toArray();
            foreach ($allowanceTypes as $t) {
                $data[$t] = [];
            }
            $data['Total'] = [];
            $result = DB::table('allowances', 'a')
                ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
                ->join('districts as d', 'd.id', '=', 'a.district_id')
                ->select('d.short_code as district', 'ws.name as scheme', DB::raw('COUNT(a.id) as applications_count'))
                ->where('a.application_date', '>=', $from)
                ->where('a.application_date', '<=', $to)
                ->groupBy('ws.id', 'd.short_code', 'd.display_code')
                ->orderBy('d.display_code', 'asc')
                ->get();
            $pending = DB::table('allowances', 'a')
                ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
                ->join('districts as d', 'd.id', '=', 'a.district_id')
                ->select('d.short_code as district', 'ws.name as scheme', DB::raw('COUNT(a.id) as applications_count'))
                ->where('a.application_date', '>=', $from)
                ->where('a.application_date', '<=', $to)
                ->where('a.status', Allowance::$STATUS_PENDING)
                ->groupBy('ws.id', 'd.short_code', 'd.display_code')
                ->orderBy('d.display_code', 'asc')
                ->get();
            $approved = DB::table('allowances', 'a')
                ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
                ->join('districts as d', 'd.id', '=', 'a.district_id')
                ->select('d.short_code as district', 'ws.name as scheme', DB::raw('COUNT(a.id) as applications_count'))
                ->where('a.application_date', '>=', $from)
                ->where('a.application_date', '<=', $to)
                ->where('a.status', Allowance::$STATUS_APPROVED)
                ->groupBy('ws.id', 'd.short_code', 'd.display_code')
                ->orderBy('d.display_code', 'asc')
                ->get();
            $rejected = DB::table('allowances', 'a')
                ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
                ->join('districts as d', 'd.id', '=', 'a.district_id')
                ->select('d.short_code as district', 'ws.name as scheme', DB::raw('COUNT(a.id) as applications_count'))
                ->where('a.application_date', '>=', $from)
                ->where('a.application_date', '<=', $to)
                ->where('a.status', Allowance::$STATUS_REJECTED)
                ->groupBy('ws.id', 'd.short_code', 'd.display_code')
                ->orderBy('d.display_code', 'asc')
                ->get();

                // $data['Approved'] = [];
                // $data['Rejected'] = [];
                // $data['Pending'] = [];
            foreach ($result as $r) {
                $data[$r->scheme][$r->district] = $r->applications_count;
                $data[$r->scheme]['Total'] = $data[$r->scheme]['Total'] ?? 0;
                $data[$r->scheme]['Total'] += $r->applications_count;
                $data['Total'][$r->district] = $data['Total'][$r->district] ?? 0;
                $data['Total'][$r->district] += $r->applications_count;
                $data['Total']['Total'] = $data['Total']['Total'] ?? 0;
                $data['Total']['Total'] += $r->applications_count;
                $data[$r->scheme]['Pending'] = $data[$r->scheme]['Pending'] ?? 0;
                $data[$r->scheme]['Approved'] = $data[$r->scheme]['Approved'] ?? 0;
                $data[$r->scheme]['Rejected'] = $data[$r->scheme]['Rejected'] ?? 0;
            }
            foreach ($approved as $a) {
                $data[$a->scheme]['Approved'] = $data[$a->scheme]['Approved'] ?? 0;
                $data[$a->scheme]['Approved'] += $a->applications_count;
            }
            foreach ($rejected as $r) {
                $data[$r->scheme]['Rejected'] = $data[$r->scheme]['Rejected'] ?? 0;
                $data[$r->scheme]['Rejected'] += $r->applications_count;
            }
            foreach ($pending as $p) {
                $data[$p->scheme]['Pending'] = $data[$p->scheme]['Pending'] ?? 0;
                $data[$p->scheme]['Pending'] += $p->applications_count;
            }
        } elseif ($user->hasPermissionTo('Dashboard: View Own District Data')) {
			//dd('okay');
            $level = 'district';
            $d = District::find($user->district_id);
            $theTaluks = $d->taluks->pluck('name')->toArray();

            foreach ($allowanceTypes as $t) {
                $data[$t] = [];
            }
            $data['Total'] = [];
            $result = DB::table('allowances', 'a')
                ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
                ->join('members as m', 'm.id', '=', 'a.member_id')
                ->join('taluks as t', 't.id', '=', 'm.taluk_id')
                ->join('districts as d', 'd.id', '=', 'a.district_id')
                ->where('a.district_id', $user->district_id)
                ->where('a.application_date', '>=', $from)
                ->where('a.application_date', '<=', $to)
                ->select('t.name as taluk', 'ws.name as scheme', DB::raw('COUNT(a.id) as applications_count'))
                ->groupBy('ws.id', 't.short_code', 't.display_code')
                ->orderBy('t.display_code', 'desc')
                ->get();
            $pending = DB::table('allowances', 'a')
                ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
                ->join('members as m', 'm.id', '=', 'a.member_id')
                ->join('taluks as t', 't.id', '=', 'm.taluk_id')
                ->join('districts as d', 'd.id', '=', 'a.district_id')
                ->where('a.district_id', $user->district_id)
                ->where('a.application_date', '>=', $from)
                ->where('a.application_date', '<=', $to)
                ->where('a.status', Allowance::$STATUS_PENDING)
                ->select('t.name as taluk', 'ws.name as scheme', DB::raw('COUNT(a.id) as applications_count'))
                ->groupBy('ws.id', 't.short_code', 't.display_code')
                ->orderBy('t.display_code', 'desc')
                ->get();
            $approved = DB::table('allowances', 'a')
                ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
                ->join('members as m', 'm.id', '=', 'a.member_id')
                ->join('taluks as t', 't.id', '=', 'm.taluk_id')
                ->join('districts as d', 'd.id', '=', 'a.district_id')
                ->where('a.district_id', $user->district_id)
                ->where('a.application_date', '>=', $from)
                ->where('a.application_date', '<=', $to)
                ->where('a.status', Allowance::$STATUS_APPROVED)
                ->select('t.name as taluk', 'ws.name as scheme', DB::raw('COUNT(a.id) as applications_count'))
                ->groupBy('ws.id', 't.short_code', 't.display_code')
                ->orderBy('t.display_code', 'desc')
                ->get();
            $rejected = DB::table('allowances', 'a')
                ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
                ->join('members as m', 'm.id', '=', 'a.member_id')
                ->join('taluks as t', 't.id', '=', 'm.taluk_id')
                ->join('districts as d', 'd.id', '=', 'a.district_id')
                ->where('a.district_id', $user->district_id)
                ->where('a.application_date', '>=', $from)
                ->where('a.application_date', '<=', $to)
                ->where('a.status', Allowance::$STATUS_REJECTED)
                ->select('t.name as taluk', 'ws.name as scheme', DB::raw('COUNT(a.id) as applications_count'))
                ->groupBy('ws.id', 't.short_code', 't.display_code')
                ->orderBy('t.display_code', 'desc')
                ->get();
                // $data['Approved'] = [];
                // $data['Rejected'] = [];
                // $data['Pending'] = [];
                foreach ($result as $r) {
                    $data[$r->scheme][$r->taluk] = $r->applications_count;
                    $data[$r->scheme]['Total'] = $data[$r->scheme]['Total'] ?? 0;
                    $data[$r->scheme]['Total'] += $r->applications_count;
                    $data['Total'][$r->taluk] = $data['Total'][$r->taluk] ?? 0;
                    $data['Total'][$r->taluk] += $r->applications_count;
                    $data['Total']['Total'] = $data['Total']['Total'] ?? 0;
                    $data['Total']['Total'] += $r->applications_count;
                    $data[$r->scheme]['Pending'] = $data[$r->scheme]['Pending'] ?? 0;
                    $data[$r->scheme]['Approved'] = $data[$r->scheme]['Approved'] ?? 0;
                    $data[$r->scheme]['Rejected'] = $data[$r->scheme]['Rejected'] ?? 0;
                }
                foreach ($approved as $a) {
                    $data[$a->scheme]['Approved'] = $data[$a->scheme]['Approved'] ?? 0;
                    $data[$a->scheme]['Approved'] += $a->applications_count;
                }
                foreach ($rejected as $r) {
                    $data[$r->scheme]['Rejected'] = $data[$r->scheme]['Rejected'] ?? 0;
                    $data[$r->scheme]['Rejected'] += $r->applications_count;
                }
                foreach ($pending as $p) {
                    $data[$p->scheme]['Pending'] = $data[$p->scheme]['Pending'] ?? 0;
                    $data[$p->scheme]['Pending'] += $p->applications_count;
                }
        }
        // $response = [
        //     'level' => $level,
        //     'data' => $data,
        // ];
        // dd($data);
        if ($level == 'state') {
            return [
                'success' => true,
                'level' => $level,
                'data' => $data,
                'branches' => $districts,
                'schemes' => $allowanceTypes
            ];
        } elseif ($level == 'district') {
            return [
                'success' => true,
                'level' => $level,
                'data' => $data,
                'branches' => $theTaluks,
                'schemes' => $allowanceTypes
            ];
        }
    }
}
?>
