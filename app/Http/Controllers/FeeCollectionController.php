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

    public function fetch($id)
    {
        $result = $this->connectorService->fetch($id);
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

}
