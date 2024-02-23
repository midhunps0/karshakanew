<?php
namespace App\Services;

use App\Models\Allowance;
use App\Models\District;
use App\Models\Member;
use App\Models\WelfareScheme;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function snapshot($year, $month)
    {
        $data = [];
        $date = Carbon::now();
        $date->year($year);
        $date->month($month);
        $from = $date->startOfMonth()->format('Y-m-d H:i:s');
        $to = $date->endOfMonth()->format('Y-m-d H:i:s');
        $pDate = Carbon::now();
        $pDate->subYear();
        $pFrom = $pDate->startOfMonth()->format('Y-m-d H:i:s');
        $pTo = $pDate->endOfMonth()->format('Y-m-d H:i:s');
        $collections = [
            'count' => [],
            'amount' => []
        ];
        $collections['count']['members'] = Member::where('reg_date', '<=', $to)
            ->userAccessControlled()
            ->count();

        $renewalsQuery = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('ft.name', 'Annual Subscription')
            ->where('fc.created_at', '>=', $from)
            ->where('fc.created_at', '<=', $to);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $renewalsQuery->where('fc.district_id', auth()->user()->district_id);
        }
        $collections['count']['renewals'] = $renewalsQuery->count();

        $renewalsQueryPrev = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('ft.name', 'Annual Subscription')
            ->where('fc.created_at', '>=', $pFrom)
            ->where('fc.created_at', '<=', $pTo);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $renewalsQueryPrev->where('fc.district_id', auth()->user()->district_id);
        }
        $collections['count']['renewals_previous'] = $renewalsQueryPrev->count();

        $collectionsQuery = DB::table('fee_collections as fc')
            ->where('fc.created_at', '>=', $from)
            ->where('fc.created_at', '<=', $to);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $collectionsQuery->where('fc.district_id', auth()->user()->district_id);
        }
        $collections['count']['collections'] = $collectionsQuery->count();

        $collectionsQueryPrev = DB::table('fee_collections as fc')
            ->where('fc.created_at', '>=', $pFrom)
            ->where('fc.created_at', '<=', $pTo);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $collectionsQueryPrev->where('fc.district_id', auth()->user()->district_id);
        }
        $collections['count']['collections_previous'] = $collectionsQueryPrev->count();

        $kuidissikaQuery = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('ft.name', 'Kudissika')
            ->where('fc.created_at', '>=', $from)
            ->where('fc.created_at', '<=', $to);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $kuidissikaQuery->where('fc.district_id', auth()->user()->district_id);
        }
        $collections['count']['kudissika'] = $kuidissikaQuery->count();

        $kuidissikaFineQuery = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('ft.name', 'Kudissika Fine')
            ->where('fc.created_at', '>=', $from)
            ->where('fc.created_at', '<=', $to);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $kuidissikaFineQuery->where('fc.district_id', auth()->user()->district_id);
        }
        $collections['count']['kudissika_fine'] = $kuidissikaFineQuery->count();

        /**** AMOUNT */

        $renewalsQueryAmt = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('ft.name', 'Annual Subscription')
            ->where('fc.created_at', '>=', $from)
            ->where('fc.created_at', '<=', $to);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $renewalsQueryAmt->where('fc.district_id', auth()->user()->district_id);
        }
        $r = $renewalsQueryAmt->selectRaw('SUM(fc.total_amount) as total')->get()->first();
        $collections['amount']['renewals'] = $r->total;

        $renewalsQueryPrevAmt = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('ft.name', 'Annual Subscription')
            ->where('fc.created_at', '>=', $pFrom)
            ->where('fc.created_at', '<=', $pTo);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $renewalsQueryPrevAmt->where('fc.district_id', auth()->user()->district_id);
        }
        $r = $renewalsQueryPrevAmt->selectRaw('SUM(fc.total_amount) as total')->get()->first();
        $collections['amount']['renewals_previous'] = $r->total;

        $collectionsQueryAmt = DB::table('fee_collections as fc')
            ->where('fc.created_at', '>=', $from)
            ->where('fc.created_at', '<=', $to);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $collectionsQueryAmt->where('fc.district_id', auth()->user()->district_id);
        }
        $r = $collectionsQueryAmt->selectRaw('SUM(fc.total_amount) as total')->get()->first();
        $collections['amount']['collections'] = $r->total;

        $collectionsQueryPrevAmt = DB::table('fee_collections as fc')
            ->where('fc.created_at', '>=', $pFrom)
            ->where('fc.created_at', '<=', $pTo);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $collectionsQueryPrevAmt->where('fc.district_id', auth()->user()->district_id);
        }
        $r = $collectionsQueryPrevAmt->selectRaw('SUM(fc.total_amount) as total')->get()->first();
        $collections['amount']['collections'] = $r->total;

        $kuidissikaQueryAmt = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('ft.name', 'Kudissika')
            ->where('fc.created_at', '>=', $from)
            ->where('fc.created_at', '<=', $to);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $kuidissikaQueryAmt->where('fc.district_id', auth()->user()->district_id);
        }
        $r = $kuidissikaQueryAmt->selectRaw('SUM(fc.total_amount) as total')->get()->first();
        $collections['amount']['kudissika'] = $r->total;

        $kuidissikaFineQueryAmt = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('ft.name', 'Kudissika Fine')
            ->where('fc.created_at', '>=', $from)
            ->where('fc.created_at', '<=', $to);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $kuidissikaFineQueryAmt->where('fc.district_id', auth()->user()->district_id);
        }
        $r = $kuidissikaFineQueryAmt->selectRaw('SUM(fc.total_amount) as total')->get()->first();
        $collections['amount']['kudissika_fine'] = $r->total;

        $data['collections'] = $collections;


        /***** ALLOWANCES DATA */
        $allowanceTypes = WelfareScheme::all()->pluck('name')->toArray();
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();

        $allowances = [];
        foreach ($allowanceTypes as $t) {
            $allowances[$t]['name'] = $t;
        }
        $allowances['Total']['name'] = 'Total';
        $pendingQuery = DB::table('allowances', 'a')
            ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
            ->select('ws.name as scheme', DB::raw('COUNT(a.id) as applications_count'))
            ->where('a.application_date', '>=', $from)
            ->where('a.application_date', '<=', $to)
            ->where('a.status', Allowance::$STATUS_PENDING)
            ->groupBy('ws.id', 'a.status')
            ->orderBy('ws.name', 'asc');
        if (!auth()->user()->hasPermissionTo('Allowance: View Report In Any District')) {
            $pendingQuery->where('a.district_id', auth()->user()->district_id);
        }
        $pending = $pendingQuery->get();
        foreach ($pending as $r) {
            $allowances[$r->scheme]['name'] = $r->scheme;
            $allowances[$r->scheme]['pending'] = $r->applications_count;
            $allowances['Total']['pending'] = $allowances['Total'] ['pending']?? 0;
            $allowances['Total']['pending'] += $r->applications_count;
        }

        $paidQueryCount = DB::table('allowances', 'a')
            ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
            ->select('ws.name as scheme', DB::raw('COUNT(a.id) as applications_count'))
            ->where('a.application_date', '>=', $from)
            ->where('a.application_date', '<=', $to)
            ->where('a.status', Allowance::$STATUS_PAID)
            ->groupBy('ws.id', 'a.status')
            ->orderBy('ws.name', 'asc');
        if (!auth()->user()->hasPermissionTo('Allowance: View Report In Any District')) {
            $paidQueryCount->where('a.district_id', auth()->user()->district_id);
        }
        $paid = $paidQueryCount->get();
        foreach ($paid as $r) {
            $allowances[$r->scheme]['name'] = $r->scheme;
            $allowances[$r->scheme]['paid_count'] = $r->applications_count;
            $allowances['Total']['paid_count'] = $allowances['Total'] ['paid_count']?? 0;
            $allowances['Total']['paid_count'] += $r->applications_count;
        }

        $paidQueryAmt = DB::table('allowances', 'a')
            ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
            ->select('ws.name as scheme', DB::raw('SUM(a.sanctioned_amount) as amount'))
            ->where('a.application_date', '>=', $from)
            ->where('a.application_date', '<=', $to)
            ->where('a.status', Allowance::$STATUS_PAID)
            ->groupBy('ws.id', 'a.status')
            ->orderBy('ws.name', 'asc');
        if (!auth()->user()->hasPermissionTo('Allowance: View Report In Any District')) {
            $paidQueryAmt->where('a.district_id', auth()->user()->district_id);
        }
        $paid = $paidQueryAmt->get();
        foreach ($paid as $r) {
            $allowances[$r->scheme]['name'] = $r->scheme;
            $allowances[$r->scheme]['paid_amount'] = $r->amount;
            $allowances['Total']['paid_amount'] = $allowances['Total'] ['paid_amount']?? 0;
            $allowances['Total']['paid_amount'] += $r->amount;
        }

        /*** TOTAL PAID */
        $totalPaidQueryCount = DB::table('allowances', 'a')
            ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
            ->select('ws.name as scheme', DB::raw('COUNT(a.id) as applications_count'))
            ->where('a.status', Allowance::$STATUS_PAID)
            ->groupBy('ws.id', 'a.status')
            ->orderBy('ws.name', 'asc');
        if (!auth()->user()->hasPermissionTo('Allowance: View Report In Any District')) {
            $totalPaidQueryCount->where('a.district_id', auth()->user()->district_id);
        }
        $paid = $totalPaidQueryCount->get();
        foreach ($paid as $r) {
            $allowances[$r->scheme]['name'] = $r->scheme;
            $allowances[$r->scheme]['total_paid_count'] = $r->applications_count;
            $allowances['Total']['total_paid_count'] = $allowances['Total'] ['total_paid_count']?? 0;
            $allowances['Total']['total_paid_count'] += $r->applications_count;
        }

        $totalPaidQueryAmt = DB::table('allowances', 'a')
            ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
            ->select('ws.name as scheme', DB::raw('SUM(a.sanctioned_amount) as amount'))
            ->where('a.status', Allowance::$STATUS_PAID)
            ->groupBy('ws.id', 'a.status')
            ->orderBy('ws.name', 'asc');
        if (!auth()->user()->hasPermissionTo('Allowance: View Report In Any District')) {
            $totalPaidQueryAmt->where('a.district_id', auth()->user()->district_id);
        }
        $paid = $totalPaidQueryAmt->get();
        foreach ($paid as $r) {
            $allowances[$r->scheme]['name'] = $r->scheme;
            $allowances[$r->scheme]['total_paid_amount'] = $r->amount;
            $allowances['Total']['total_paid_amount'] = $allowances['Total'] ['total_paid_amount']?? 0;
            $allowances['Total']['total_paid_amount'] += $r->amount;
        }


        $data['allowances'] = $allowances;
            // dd($allowances);
        return $data;
    }
}
