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
        $this->editView = 'admin.feecollections.receipt_edit';
    }

    public function update($id)
    {
        $result = $this->connectorService->update($this->request->all(), $id);
        return response()->json($result);
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
                    'page' => $page,
                    'datetype' => $request->input('datetype', 'receipt_date')
                ]);
            }
            return $this->buildResponse('admin.feecollections.report', ['receipts' => $result]);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }

    public function fullReport(Request $request)
    {
        $start = $request->input('start', null);
        $end = $request->input('end', null);
        $page = $request->input('page', 1);
            $result = [];

        if ($start != null || $end != null) {
            $result = $this->connectorService->report([
                'start' => $start,
                'end' => $end,
                'fullreport' => true,
                'datetype' => $request->input('datetype', 'receipt_date')
            ]);
        }
        return response()->json([
            'receipts' => $result
        ]);
    }

    public function search()
    {
        $start = $this->request->input('start', null);
        $end = $this->request->input('end', null);
        $page = $this->request->input('page', 1);
        $receiptNo = $this->request->input('receipt_no', null);
        try {
            if ($start == null && $end == null && $receiptNo == null) {
                $result = [];
            } else {
                $result = $this->connectorService->search([
                    'start' => $start,
                    'end' => $end,
                    'page' => $page,
                    'searchBy' => $this->request->input('searchBy', 'receipt_no'),
                    'receipt_no' => $receiptNo
                ]);
            }
            return $this->buildResponse('admin.feecollections.search', ['receipts' => $result]);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }

}
