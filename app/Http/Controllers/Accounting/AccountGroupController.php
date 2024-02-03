<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteAccountGroupRequest;
use App\Http\Requests\StoreAccountGroupRequest;
use App\Http\Requests\UpdateAccountGroupRequest;
use App\Services\AccountGroupService;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class AccountGroupController extends SmartController
{
    use HasMVConnector;

    public function __construct(Request $request, AccountGroupService $service)
    {
        $this->request = $request;
        $this->connectorService = $service;
        $this->indexView = 'admin.index';
    }

    // public function index()
    // {
    //     return $this->accountGroupService->all();
    // }

    // public function show($id)
    // {
    //     try {
    //         $result = $this->accountGroupService->findOrFail($id);
    //         return $this->buildResponse(
    //             'admin.accounts.group.show',
    //             [
    //                 'success' => true,
    //                 'data' => $result
    //             ]
    //         );
    //     } catch (\Throwable $e) {
    //         return $this->buildResponse(
    //             'admin.accounts.group.show',
    //             [
    //                 'success' => false,
    //                 'error' => $e->__toString()
    //             ]
    //         );
    //     }
    // }
    /*
    public function store(StoreAccountGroupRequest $request)
    {
        $data = $request->validated();
        if (!isset($data['district_id'])) {
            $data['district_id'] = auth()->user()->district;
        }

        try {
            $group = $this->accountGroupService->create($data);
            return response()->json(
                [
                    'success' => true,
                    'data' => $group
                ]
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'success' => false,
                    'data' => $e->__toString()
                ]
            );
        }
    }

    public function update(UpdateAccountGroupRequest $request, $id)
    {
        try {
            $group = $this->accountGroupService->update($request->validated(), $id);

            return response()->json(
                [
                    'success' => true,
                    'data' => $group
                ]
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'success' => false,
                    'data' => $e->__toString()
                ]
            );
        }
    }

    public function delete(DeleteAccountGroupRequest $request, $id)
    {
        try {
            $group = $this->accountGroupService->delete($id);
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Deleted account group'
                ]
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Couldn\'t delete the account group'
                ]
            );
        }
    }
    */
}
