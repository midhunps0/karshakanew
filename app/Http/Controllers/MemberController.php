<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\District;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\MemberService;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use App\Http\Requests\FeesCollectionStoreRequest;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Requests\OldFeesCollectionStoreRequest;
use App\Models\Member;
use App\Models\WelfareScheme;
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
        if ($this->request->input('m') != null) {
            $member = Member::find($this->request->input('m'));
        }
        return $this->buildResponse('admin.members.merge', [
            'member' => $member
        ]);
    }

    // public function transferForm($id)
    // {
    //     return $this->buildResponse(
    //         'admin.members.transfer',
    //         $this->connectorService->getTransferFormData($id)
    //     );
    // }
}
