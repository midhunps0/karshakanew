<?php

namespace App\Http\Controllers;

use App\Exports\MembersExport;
use Throwable;
use App\Models\Member;
use App\Models\District;
use App\Helpers\AppHelper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\WelfareScheme;
use App\Services\MemberService;
use Maatwebsite\Excel\Facades\Excel;
use GrahamCampbell\ResultType\Success;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use App\Http\Requests\FeesCollectionStoreRequest;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Requests\OldFeesCollectionStoreRequest;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class MemberController extends SmartController
{
    use HasMVConnector;

    private $searchView = 'easyadmin::admin.searchform';

    public function __construct(public MemberService $connectorService, Request $request){
        parent::__construct($request);
        $this->indexView = 'admin.index';
    }

    public function create()
    {
        $aadhaarNo = $this->request->input('an', null);
        $view = 'admin.members.verify';
        $data = [];
        $fr = $this->request->header('X-FR', null);

        if (isset($aadhaarNo) && isset($fr)) {
            $view = 'easyadmin::admin.form';
            $data = $this->connectorService->getCreatePageData($aadhaarNo);
        }
        try {
            return $this->buildResponse($view, $data);
        } catch (AuthorizationException $e) {
            return $this->buildResponse($this->unauthorisedView);
        } catch (Throwable $e) {
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }

    public function show($id)
    {
        $view = 'admin.members.show';
        try {
            $member = $this->connectorService->show($id);
            $schemes = WelfareScheme::all();
            $enabledSchemes = [];
            foreach ($schemes as $s) {
                $enabledSchemes[$s->code] = $s->is_enabled;
            }
            return $this->buildResponse(
                $view,
                [
                    'member' => $member,
                    'enabledSchemes' => $enabledSchemes
                ]
            );
        } catch (AuthorizationException $e) {
            info($e);
            return $this->buildResponse($this->unauthorisedView);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }

    public function fetch($id)
    {
        try {
            $result = $this->connectorService->fetch($id);
            return response()->json(
                [
                    'member' => $result
                ]
            );
        } catch (AuthorizationException $e) {
            info($e);
            return $this->buildResponse($this->unauthorisedView);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }

    public function search()
    {
        // $result = $this->connectorService->search($this->request->all());

        $view = 'admin.members.search';
        try {
            $data = $this->connectorService->getSearchPageData();
            return $this->buildResponse($view, $data);
        } catch (AuthorizationException $e) {
            info($e);
            return $this->buildResponse($this->unauthorisedView);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }

    public function verifyAadhaar($aadhaarNo)
    {
        try {
            return response()->json(
                $this->connectorService->verifyAadhaar($aadhaarNo)
            );
        } catch (AuthorizationException $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'error' => $e->__toString()
                ]
            );
        } catch (Throwable $e) {
            info($e);
            return response()->json(
                [
                    'status' => 'error',
                    'error' => $e->__toString()
                ]
            );
        }
    }

    public function annualFeesPeriod($id, Request $request)
    {
        $result = $this->connectorService->annualFeesPeriod($id, $request->input('tenure'));
        try {
            $result = $this->connectorService->annualFeesPeriod($id, $request->input('tenure'));
            return response()->json($result);
        } catch (AuthorizationException $e) {
            info($e);
            return $this->buildResponse($this->unauthorisedView);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }

    public function suggestionslist()
    {
        try {
            $result = $this->connectorService->suggestionslist($this->request->all());
            return response()->json(
                [
                    'members' => $result
                ]
            );
        } catch (AuthorizationException $e) {
            info($e);
            return $this->buildResponse($this->unauthorisedView);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }

    public function storeFeesCollection($id, FeesCollectionStoreRequest $request)
    {
        try {
            $result = $this->connectorService->storeFeesCollection($id, $request->validated());
            return response()->json($result);
        } catch (AuthorizationException $e) {
            info($e);
            return $this->buildResponse($this->unauthorisedView);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }

    public function storeBulkFees(Request $request)
    {
        try {
            $result = $this->connectorService->storeBulkFees($request->all());
            return response()->json($result);
        } catch (AuthorizationException $e) {
            info($e);
            return $this->buildResponse($this->unauthorisedView);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }

    public function storeOldFeesCollection($id, OldFeesCollectionStoreRequest $request)
    {
        try {
            $result = $this->connectorService->storeFeesCollection($id, $request->validated());
            return response()->json($result);
        } catch (AuthorizationException $e) {
            info($e);
            return $this->buildResponse($this->unauthorisedView);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }

    public function unapprovedMembers(MemberService $memberService)
    {
        $result = $memberService->unapprovedMembers($this->request->all());
        return $this->buildResponse('admin.members.unapproved', ['members' => $result]);
    }

    public function sync()
    {
        $member = null;
        $status = 'ok';
        if ($this->request->input('m') != null) {
            $member = Member::find($this->request->input('m'));
            if ($member->merged == 1) {
                $member = null;
                $status = 'The member you tried to fetch data for, was already updated.';
            }
        }
        return $this->buildResponse('admin.members.merge', [
            'member' => $member,
            'status' => $status
        ]);
    }

    public function fetchMemberFromOld()
    {
        $membershipNo = $this->request->input('membership_no');
        $memberId = $this->request->input('member_id');

        try {
            return response()->json(
                $this->connectorService->fetchMemberCurl($membershipNo, $memberId)
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->__toString()
            ]);
        }
    }

    public function reportsIndex()
    {
        return $this->buildResponse('admin.members.reports-index');
    }

    public function reportGenders(Request $request)
    {
        $districts = District::memberEditAllowed()->withoutHO()->get();

        $members = $this->connectorService->memberReport(
            searches: $request->input('searches'),
            page: $request->input('page'));
        return $this->buildResponse('admin.members.report-gender', [
            'districts' => $districts,
            'data' => $members
        ]);
    }

    public function downloadGenders(Request $request)
    {
        // $districts = District::memberEditAllowed()->withoutHO()->get();

        $members = $this->connectorService->memberReport(
            searches: $request->input('searches'),
            page: $request->input('page'),
            download: true
        );
        $columns = [
            'name',
            'membership_no',
            'aadhaar_no',
            'permanent_address',
            'gender',
            'district.name',
            'taluk.name',
        ];
        $headings = [
            'Name',
            'Membership No',
            'Aadhaar No',
            'Permanent Address',
            'Gender',
            'District',
            'Taluk'
        ];
        return Excel::download(new MembersExport($members, $columns, $headings), 'members.csv');
    }

    public function reportNew(Request $request)
    {
        $districts = District::memberEditAllowed()->withoutHO()->get();
        $searches = $request->input('searches');
        $from = null;
        $to = null;
        if ($searches != null) {
            $from = explode('::', $searches[0])[2];
            $fromArr = explode('-', $from);
            $fromArr = array_reverse($fromArr);
            $from = implode('-', $fromArr);

            $to = explode('::', $searches[1])[2];
            $toArr = explode('-', $to);
            $toArr = array_reverse($toArr);
            $to = implode('-', $toArr);
        }
        $members = $this->connectorService->memberReport(
            searches: $searches,
            page: $request->input('page'));
        return $this->buildResponse('admin.members.report-new', [
            'districts' => $districts,
            'data' => $members,
            'from' => $from,
            'to' => $to
        ]);
    }

    public function downloadNew(Request $request)
    {
        // $districts = District::memberEditAllowed()->withoutHO()->get();
        $searches = $request->input('searches');
        $from = null;
        $to = null;
        if ($searches != null) {
            $from = explode('::', $searches[0])[2];
            $fromArr = explode('-', $from);
            $fromArr = array_reverse($fromArr);
            $from = implode('-', $fromArr);

            $to = explode('::', $searches[1])[2];
            $toArr = explode('-', $to);
            $toArr = array_reverse($toArr);
            $to = implode('-', $toArr);
        }
        $members = $this->connectorService->memberReport(
            searches: $searches,
            page: $request->input('page'),
            download: true
        );

        $columns = [
            'name',
            'membership_no',
            'aadhaar_no',
            'reg_date',
            'permanent_address',
            'district.name',
            'taluk.name',
        ];
        $headings = [
            'Name',
            'Membership No',
            'Aadhaar No',
            'Registration Date',
            'Permanent Address',
            'District',
            'Taluk'
        ];
        return Excel::download(new MembersExport($members, $columns, $headings), 'members.csv');
    }

    public function reportStatus(Request $request)
    {
        $districts = District::memberEditAllowed()->withoutHO()->get();

        $members = $this->connectorService->memberReport(
            searches: $request->input('searches'),
            page: $request->input('page'));
        return $this->buildResponse('admin.members.report-status', [
            'districts' => $districts,
            'data' => $members
        ]);
    }

    public function reportCustom(Request $request)
    {
        $districts = District::memberEditAllowed()->withoutHO()->get();

        $members = $this->connectorService->memberReport(
            searches: $request->input('searches'),
            page: $request->input('page')
        );

        return $this->buildResponse('admin.members.report-custom', [
            'districts' => $districts,
            'data' => $members
        ]);
    }

    public function downloadStatus(Request $request)
    {
        // $districts = District::memberEditAllowed()->withoutHO()->get();

        $members = $this->connectorService->memberReport(
            searches: $request->input('searches'),
            page: $request->input('page'),
            download: true
        );
            $columns = [
                'name',
                'membership_no',
                'aadhaar_no',
                'permanent_address',
                'active',
                'district.name',
                'taluk.name',
            ];
            $boolCols = [
                'active' => ['Active', 'Inactive']
            ];
            $headings = [
                'Name',
                'Membership No',
                'Aadhaar No',
                'Permanent Address',
                'Status',
                'District',
                'Taluk'
            ];
            return Excel::download(new MembersExport($members, $columns, $headings, $boolCols), 'members.csv');
    }

    public function downloadCustomReport(Request $request)
    {
        // $districts = District::memberEditAllowed()->withoutHO()->get();

        $members = $this->connectorService->memberReport(
            searches: $request->input('searches'),
            page: $request->input('page'),
            download: true,
        );
            // $columns = [
            //     'name',
            //     'membership_no',
            //     'aadhaar_no',
            //     'permanent_address',
            //     'active',
            //     'district.name',
            //     'taluk.name',
            // ];
            $columns = explode(',',$request->columns);
            $boolCols = [
                'active' => ['Active', 'Inactive']
            ];
            $headings = [
                'Name',
                'Membership No',
                'Aadhaar No',
                'Permanent Address',
                'Status',
                'District',
                'Taluk'
            ];
            return Excel::download(new MembersExport($members, $columns, $headings, $boolCols), 'members.csv');
    }

    public function report()
    {
        if (is_string($this->indexView)) {
            $view = $this->indexView ?? 'admin.'.Str::plural($this->getItemName()).'.index';
        } elseif(is_array($this->indexView)) {
            $target = $this->request->input('x_target');
            $view = isset($target) && isset($this->indexView[$target]) ? $this->indexView[$target] : $this->indexView['default'];
        }

        try {
            $result = $this->connectorService->report(
                intval($this->request->input('items_count', 100)),
                $this->request->input('page'),
                $this->request->input('search', []),
                $this->request->input('sort', []),
                $this->request->input('filter', []),
                $this->request->input('adv_search', []),
                $this->request->input('index_mode', true),
                $this->request->input('selected_ids', ''),
                'results',
            );
            return $this->buildResponse($view, $result);
        } catch (AuthorizationException $e) {
            info($e);
            return $this->buildResponse($this->unauthorisedView);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
        // try {
        //     $result = $this->connectorService->report($this->request->all());
        //     return $this->buildResponse('members.report', $result);
        // } catch (\Throwable $e) {
        //     info($e);
        //     return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        // }
    }

    // public function transferForm($id)
    // {
    //     return $this->buildResponse(
    //         'admin.members.transfer',
    //         $this->connectorService->getTransferFormData($id)
    //     );
    // }
}
