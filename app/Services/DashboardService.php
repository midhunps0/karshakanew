<?php
namespace App\Services;

use App\Helpers\AppHelper;
use App\Models\District;
use App\Models\FeeType;
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
}
?>
