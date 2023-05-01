<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use App\Services\FeeCollectionService;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Illuminate\Auth\Access\AuthorizationException;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class FeeCollectionController extends SmartController
{
    use HasMVConnector;

    public function __construct(public FeeCollectionService $connectorService, Request $request){
        parent::__construct($request);
        // $this->itemName = 'districts';
        // $this->indexView = 'easyadmin::admin.indexpanel';
        $this->createView = 'admin.receipt_create';
        // $this->editView = 'accesscontrol::roles.edit';
    }

    public function createOld()
    {
        return $this->buildResponse(
            'admin.old_receipt_create',
            [
                'form' => [
                    'id' => 'form_old_fee_collections_create',
                ]
            ]
        );
    }

    public function createBulk()
    {
        return $this->buildResponse(
            'admin.bulk_receipt_create',
            [
                'form' => [
                    'id' => 'form_bulk_fee_collections_create',
                ]
            ]
        );
    }

    public function fetch($id)
    {
        try {
            $result = $this->connectorService->fetch($id);
            return response()->json([
                'receipt' => $result
            ]);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }

    public function reportForm(Request $request)
    {
        info($request);
        $start = $request->input('start', null);
        $end = $request->input('end', null);
        $page = $request->input('page', 1);
        try {
            if ($start == null && $end == null) {
                $result = [];
            } else {
                $result = $this->connectorService->report([
                    'start' => $start,
                    'end' => $end,
                    'page' => $page
                ]);
            }
            return $this->buildResponse('admin.feecollections.report', ['receipts' => $result]);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }

    // public function reportData(Request $request)
    // {
    //     # code...
    // }

}
