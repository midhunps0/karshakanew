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
use Ynotz\SmartPages\Http\Controllers\SmartController;

class MemberController extends SmartController
{
    use HasMVConnector;

    private $searchView = 'easyadmin::admin.searchform';

    public function __construct(public MemberService $connectorService, Request $request){
        parent::__construct($request);
        $this->indexView = 'admin.index';
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
}