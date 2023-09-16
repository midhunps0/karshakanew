<?php

namespace App\Http\Controllers;

use App\Services\MemberTransferService;
use Illuminate\Http\Request;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class MemberTransferController extends SmartController
{
    use HasMVConnector;

    public function __construct(public MemberTransferService $connectorService, Request $request){
        parent::__construct($request);
        // $this->itemName = 'districts';
        $this->indexView = 'admin.index';
        // $this->createView = 'accesscontrol::roles.create';
        // $this->editView = 'accesscontrol::roles.edit';
    }

    public function transferForm($id)
    {
        return $this->buildResponse(
            'admin.members.transfer',
            $this->connectorService->getTransferFormData($id)
        );
    }

    public function transferEditForm($id)
    {
        return $this->buildResponse(
            'admin.members.transfer_edit',
            $this->connectorService->getTransferEditFormData($id)
        );
    }

    public function transferRequests()
    {
        return $this->buildResponse('admin.members.transfer-requests', [
            'placed' => $this->connectorService->requestsPlaced(),
            'received' => $this->connectorService->requestsReceived(),
        ]);
    }

    public function approve($id)
    {
        try {
            $transfer = $this->connectorService->approve($id);
            return response()->json([
                'success' => true,
                'transfer' => $transfer
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->__toString()
            ]);
        }
    }
}
