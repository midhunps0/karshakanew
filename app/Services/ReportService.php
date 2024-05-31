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
    public function snapshot($year, $month, $chosenDistrictId = null)
    {
        $data = [];
        $from = Carbon::create($year, $month)->month($month)->startOfMonth()->format('Y-m-d H:i:s');
        $to = Carbon::create($year, $month)->endOfMonth()->format('Y-m-d H:i:s');
        $pDate = Carbon::now();
        $pDate->subYear();
        $pFrom = $pDate->startOfMonth()->format('Y-m-d H:i:s');
        $pTo = $pDate->endOfMonth()->format('Y-m-d H:i:s');
        $collections = [
            'count' => [
                'members' => 0,
                'renewals' => 0,
                'renewals_previous' => 0,
                'collections' => 0,
                'collections_previous' => 0,
                'kudissika' => 0,
                'kudissika_fine' => 0,
            ],
            'amount' => [
                'renewals' => 0,
                'renewals_previous' => 0,
                'collections' => 0,
                'collections_previous' => 0,
                'kudissika' => 0,
                'kudissika_fine' => 0,
            ]
        ];

        $amshadayam = config('generalSettings.amshadhayam');
        $kudissika = config('generalSettings.kudissika');
        $kudissika_fine = config('generalSettings.kudissika_fine');

        $membersQuery = Member::where('reg_date', '<=', $to);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $membersQuery->where('district_id', auth()->user()->district_id);
        } else if (isset($chosenDistrictId)) {
            $membersQuery->where('district_id', $chosenDistrictId);
        }
        $collections['count']['members'] = $membersQuery->count();

        /***** */
        $currentQuery = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('fc.receipt_date', '>=', $from)
            ->where('fc.receipt_date', '<=', $to)
            ->whereNull('fc.deleted_at');
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $currentQuery->where('fc.district_id', auth()->user()->district_id);
        } else if (isset($chosenDistrictId)) {
            $currentQuery->where('fc.district_id', $chosenDistrictId);
        }
            $currentQuery->groupBy('ft.name')
            ->select(DB::raw('ft.name, COUNT(DISTINCT fc.id) as fcount, SUM(fc.total_amount) as total_amount'));
        info('test sql:');
        info($currentQuery->toSql());
        $currentResult = $currentQuery->get();
        info($currentResult);

        foreach ($currentResult as $item) {
            $collections['count']['collections'] += $item->fcount;
            if ($item->name == $amshadayam) {
                $collections['count']['renewals'] += $item->fcount;
            }
            if ($item->name == $kudissika) {
                $collections['count']['kudissika'] += $item->fcount;
            }
            if ($item->name == $kudissika_fine) {
                $collections['count']['kudissika_fine'] += $item->fcount;
            }

            $collections['amount']['collections'] += $item->total_amount;
            if ($item->name == $amshadayam) {
                $collections['amount']['renewals'] += $item->total_amount;
            }
            if ($item->name == $kudissika) {
                $collections['amount']['kudissika'] += $item->total_amount;
            }
            if ($item->name == $kudissika_fine) {
                $collections['amount']['kudissika_fine'] += $item->total_amount;
            }
        }
        /******** */

        /***** */
        $prevQuery = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('fc.created_at', '>=', $pFrom)
            ->where('fc.created_at', '<=', $pTo);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $prevQuery->where('fc.district_id', auth()->user()->district_id);
        } else if (isset($chosenDistrictId)) {
            $prevQuery->where('fc.district_id', $chosenDistrictId);
        }
        $prevQuery->groupBy('ft.name')
        ->select(DB::raw('ft.name, COUNT(DISTINCT fc.id) as fcount, SUM(fc.total_amount) as total_amount'));

        info('test sql:');
        info($prevQuery->toSql());

        $prevResult = $prevQuery->get();

        info($prevResult);

        foreach ($prevResult as $item) {
            $collections['count']['collections_previous'] += $item->fcount;
            if ($item->name == $amshadayam) {
                $collections['count']['renewals_previous'] += $item->fcount;
            }

            $collections['amount']['collections_previous'] += $item->total_amount;
            if ($item->name == $amshadayam) {
                $collections['amount']['renewals_previous'] += $item->total_amount;
            }
        }
        /******** */

        $data['collections'] = $collections;

        /*

        $renewalsQuery = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('ft.name', 'Amshadhayam')
            ->where('fc.created_at', '>=', $from)
            ->where('fc.created_at', '<=', $to);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $renewalsQuery->where('fc.district_id', auth()->user()->district_id);
        } else if (isset($chosenDistrictId)) {
            $renewalsQuery->where('fc.district_id', $chosenDistrictId);
        }
        $collections['count']['renewals'] = $renewalsQuery->count();

        $renewalsQueryPrev = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('ft.name', 'Amshadhayam')
            ->where('fc.created_at', '>=', $pFrom)
            ->where('fc.created_at', '<=', $pTo);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $renewalsQueryPrev->where('fc.district_id', auth()->user()->district_id);
        } else if (isset($chosenDistrictId)) {
            $renewalsQueryPrev->where('fc.district_id', $chosenDistrictId);
        }
        $collections['count']['renewals_previous'] = $renewalsQueryPrev->count();

        $collectionsQuery = DB::table('fee_collections as fc')
            ->where('fc.created_at', '>=', $from)
            ->where('fc.created_at', '<=', $to);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $collectionsQuery->where('fc.district_id', auth()->user()->district_id);
        } else if (isset($chosenDistrictId)) {
            $collectionsQuery->where('fc.district_id', $chosenDistrictId);
        }
        $collections['count']['collections'] = $collectionsQuery->count();

        $collectionsQueryPrev = DB::table('fee_collections as fc')
            ->where('fc.created_at', '>=', $pFrom)
            ->where('fc.created_at', '<=', $pTo);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $collectionsQueryPrev->where('fc.district_id', auth()->user()->district_id);
        } else if (isset($chosenDistrictId)) {
            $collectionsQueryPrev->where('fc.district_id', $chosenDistrictId);
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
        } else if (isset($chosenDistrictId)) {
            $kuidissikaQuery->where('fc.district_id', $chosenDistrictId);
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
        } else if (isset($chosenDistrictId)) {
            $kuidissikaFineQuery->where('fc.district_id', $chosenDistrictId);
        }
        $collections['count']['kudissika_fine'] = $kuidissikaFineQuery->count();


        --**** AMOUNT ****--


        $renewalsQueryAmt = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('ft.name', 'Amshadhayam')
            ->where('fc.created_at', '>=', $from)
            ->where('fc.created_at', '<=', $to);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $renewalsQueryAmt->where('fc.district_id', auth()->user()->district_id);
        } else if (isset($chosenDistrictId)) {
            $renewalsQueryAmt->where('fc.district_id', $chosenDistrictId);
        }
        $r = $renewalsQueryAmt->selectRaw('SUM(fc.total_amount) as total')->get()->first();
        $collections['amount']['renewals'] = $r->total;

        $renewalsQueryPrevAmt = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('ft.name', 'Amshadhayam')
            ->where('fc.created_at', '>=', $pFrom)
            ->where('fc.created_at', '<=', $pTo);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $renewalsQueryPrevAmt->where('fc.district_id', auth()->user()->district_id);
        } else if (isset($chosenDistrictId)) {
            $renewalsQueryPrevAmt->where('fc.district_id', $chosenDistrictId);
        }
        $r = $renewalsQueryPrevAmt->selectRaw('SUM(fc.total_amount) as total')->get()->first();
        $collections['amount']['renewals_previous'] = $r->total;

        $collectionsQueryAmt = DB::table('fee_collections as fc')
            ->where('fc.created_at', '>=', $from)
            ->where('fc.created_at', '<=', $to);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $collectionsQueryAmt->where('fc.district_id', auth()->user()->district_id);
        } else if (isset($chosenDistrictId)) {
            $collectionsQueryAmt->where('fc.district_id', $chosenDistrictId);
        }
        $r = $collectionsQueryAmt->selectRaw('SUM(fc.total_amount) as total')->get()->first();
        $collections['amount']['collections'] = $r->total;

        $collectionsQueryPrevAmt = DB::table('fee_collections as fc')
            ->where('fc.created_at', '>=', $pFrom)
            ->where('fc.created_at', '<=', $pTo);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $collectionsQueryPrevAmt->where('fc.district_id', auth()->user()->district_id);
        } else if (isset($chosenDistrictId)) {
            $collectionsQueryPrevAmt->where('fc.district_id', $chosenDistrictId);
        }
        $r = $collectionsQueryPrevAmt->selectRaw('SUM(fc.total_amount) as total')->get()->first();
        $collections['amount']['collections_previous'] = $r->total;

        $kuidissikaQueryAmt = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('ft.name', 'Kudissika')
            ->where('fc.created_at', '>=', $from)
            ->where('fc.created_at', '<=', $to);
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $kuidissikaQueryAmt->where('fc.district_id', auth()->user()->district_id);
        } else if (isset($chosenDistrictId)) {
            $kuidissikaQueryAmt->where('fc.district_id', $chosenDistrictId);
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
        } else if (isset($chosenDistrictId)) {
            $kuidissikaFineQueryAmt->where('fc.district_id', $chosenDistrictId);
        }
        $r = $kuidissikaFineQueryAmt->selectRaw('SUM(fc.total_amount) as total')->get()->first();
        $collections['amount']['kudissika_fine'] = $r->total;

        $data['collections'] = $collections;

        */

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

        /***** */
        $allowancesQuery = DB::table('allowances', 'a')
            ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
            ->select('ws.name as scheme', 'a.status as status', DB::raw('COUNT(a.id) as applications_count'), DB::raw('SUM(a.sanctioned_amount) as amount'))
            ->where('a.application_date', '>=', $from)
            ->where('a.application_date', '<=', $to)
            // ->where('a.status', Allowance::$STATUS_PENDING)
            ->groupBy('ws.id', 'a.status')
            ->orderBy('ws.name', 'asc');
        if (!auth()->user()->hasPermissionTo('Allowance: View Report In Any District')) {
            $allowancesQuery->where('a.district_id', auth()->user()->district_id);
        }
        info('allowances query');
        info($allowancesQuery->toSql());
        $allowancesResult = $allowancesQuery->get();
        foreach ($allowancesResult as $r) {
            $allowances[$r->scheme]['name'] = $r->scheme;
            if ($r->status = Allowance::$STATUS_PENDING) {
                $allowances[$r->scheme]['pending'] = $r->applications_count;
                $allowances['Total']['pending'] = $allowances['Total']['pending']?? 0;
                $allowances['Total']['pending'] += $r->applications_count;
            }
            if ($r->status = Allowance::$STATUS_PAID) {
                $allowances[$r->scheme]['paid_count'] = $r->applications_count;
                $allowances[$r->scheme]['paid_amount'] = $r->amount;
                $allowances['Total']['paid_amount'] = $allowances['Total']['paid_amount'] ?? 0;
                $allowances['Total']['paid_amount'] += $r->amount;
            }
        }

        /******* */

        // $pendingQuery = DB::table('allowances', 'a')
        //     ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
        //     ->select('ws.name as scheme', DB::raw('COUNT(a.id) as applications_count'))
        //     ->where('a.application_date', '>=', $from)
        //     ->where('a.application_date', '<=', $to)
        //     ->where('a.status', Allowance::$STATUS_PENDING)
        //     ->groupBy('ws.id', 'a.status')
        //     ->orderBy('ws.name', 'asc');
        // if (!auth()->user()->hasPermissionTo('Allowance: View Report In Any District')) {
        //     $pendingQuery->where('a.district_id', auth()->user()->district_id);
        // }
        // $pending = $pendingQuery->get();
        // foreach ($pending as $r) {
        //     $allowances[$r->scheme]['name'] = $r->scheme;
        //     $allowances[$r->scheme]['pending'] = $r->applications_count;
        //     $allowances['Total']['pending'] = $allowances['Total'] ['pending']?? 0;
        //     $allowances['Total']['pending'] += $r->applications_count;
        // }

        // $paidQueryCount = DB::table('allowances', 'a')
        //     ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
        //     ->select('ws.name as scheme', DB::raw('COUNT(a.id) as applications_count'))
        //     ->where('a.application_date', '>=', $from)
        //     ->where('a.application_date', '<=', $to)
        //     ->where('a.status', Allowance::$STATUS_PAID)
        //     ->groupBy('ws.id', 'a.status')
        //     ->orderBy('ws.name', 'asc');
        // if (!auth()->user()->hasPermissionTo('Allowance: View Report In Any District')) {
        //     $paidQueryCount->where('a.district_id', auth()->user()->district_id);
        // }
        // $paid = $paidQueryCount->get();
        // foreach ($paid as $r) {
        //     $allowances[$r->scheme]['name'] = $r->scheme;
        //     $allowances[$r->scheme]['paid_count'] = $r->applications_count;
        //     $allowances['Total']['paid_count'] = $allowances['Total']['paid_count']?? 0;
        //     $allowances['Total']['paid_count'] += $r->applications_count;
        // }

        // $paidQueryAmt = DB::table('allowances', 'a')
        //     ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
        //     ->select('ws.name as scheme', DB::raw('SUM(a.sanctioned_amount) as amount'))
        //     ->where('a.application_date', '>=', $from)
        //     ->where('a.application_date', '<=', $to)
        //     ->where('a.status', Allowance::$STATUS_PAID)
        //     ->groupBy('ws.id', 'a.status')
        //     ->orderBy('ws.name', 'asc');
        // if (!auth()->user()->hasPermissionTo('Allowance: View Report In Any District')) {
        //     $paidQueryAmt->where('a.district_id', auth()->user()->district_id);
        // }
        // $paid = $paidQueryAmt->get();
        // foreach ($paid as $r) {
        //     $allowances[$r->scheme]['name'] = $r->scheme;
        //     $allowances[$r->scheme]['paid_amount'] = $r->amount;
        //     $allowances['Total']['paid_amount'] = $allowances['Total'] ['paid_amount']?? 0;
        //     $allowances['Total']['paid_amount'] += $r->amount;
        // }

        /*** TOTAL ALL-TIME PAID */

        /******* */
        $totalPaidQuery = DB::table('allowances', 'a')
            ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
            ->select('ws.name as scheme', 'a.status as status', DB::raw('COUNT(a.id) as applications_count'), DB::raw('SUM(a.sanctioned_amount) as amount'))
            ->where('a.status', Allowance::$STATUS_PAID)
            ->groupBy('ws.id', 'a.status')
            ->orderBy('ws.name', 'asc');
        if (!auth()->user()->hasPermissionTo('Allowance: View Report In Any District')) {
            $totalPaidQuery->where('a.district_id', auth()->user()->district_id);
        }
        $paid = $totalPaidQuery->get();
        foreach ($paid as $r) {
            $allowances[$r->scheme]['name'] = $r->scheme;
            if ($r->status = Allowance::$STATUS_PAID) {
                $allowances[$r->scheme]['total_paid_count'] = $r->applications_count;
                $allowances['Total']['total_paid_count'] = $allowances['Total']['total_paid_count'] ?? 0;
                $allowances['Total']['total_paid_count'] += $r->applications_count;
                $allowances[$r->scheme]['total_paid_amount'] = $r->amount;
                $allowances['Total']['total_paid_amount'] = $allowances['Total'] ['total_paid_amount']?? 0;
                $allowances['Total']['total_paid_amount'] += $r->amount;
            }
        }

        /****** */
        // $totalPaidQueryCount = DB::table('allowances', 'a')
        //     ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
        //     ->select('ws.name as scheme', DB::raw('COUNT(a.id) as applications_count'))
        //     ->where('a.status', Allowance::$STATUS_PAID)
        //     ->groupBy('ws.id', 'a.status')
        //     ->orderBy('ws.name', 'asc');
        // if (!auth()->user()->hasPermissionTo('Allowance: View Report In Any District')) {
        //     $totalPaidQueryCount->where('a.district_id', auth()->user()->district_id);
        // }
        // $paid = $totalPaidQueryCount->get();
        // foreach ($paid as $r) {
        //     $allowances[$r->scheme]['name'] = $r->scheme;
        //     $allowances[$r->scheme]['total_paid_count'] = $r->applications_count;
        //     $allowances['Total']['total_paid_count'] = $allowances['Total'] ['total_paid_count']?? 0;
        //     $allowances['Total']['total_paid_count'] += $r->applications_count;
        // }

        // $totalPaidQueryAmt = DB::table('allowances', 'a')
        //     ->join('welfare_schemes as ws', 'a.welfare_scheme_id', '=', 'ws.id')
        //     ->select('ws.name as scheme', DB::raw('SUM(a.sanctioned_amount) as amount'))
        //     ->where('a.status', Allowance::$STATUS_PAID)
        //     ->groupBy('ws.id', 'a.status')
        //     ->orderBy('ws.name', 'asc');
        // if (!auth()->user()->hasPermissionTo('Allowance: View Report In Any District')) {
        //     $totalPaidQueryAmt->where('a.district_id', auth()->user()->district_id);
        // }
        // $paid = $totalPaidQueryAmt->get();
        // foreach ($paid as $r) {
        //     $allowances[$r->scheme]['name'] = $r->scheme;
        //     $allowances[$r->scheme]['total_paid_amount'] = $r->amount;
        //     $allowances['Total']['total_paid_amount'] = $allowances['Total'] ['total_paid_amount']?? 0;
        //     $allowances['Total']['total_paid_amount'] += $r->amount;
        // }

        $data['allowances'] = $allowances;
            // dd($allowances);
        info('data');
        info($data);
        return $data;
    }
}
