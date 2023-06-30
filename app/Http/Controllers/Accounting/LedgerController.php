<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use App\Http\Requests\UpdateLedgerAccountRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Accounting\LedgerAccount;
use App\Http\Requests\StoreLedgerAccountRequest;
use Illuminate\Validation\UnauthorizedException;
use App\Http\Requests\UpdateLedgerAccountRequest;
use App\Services\LedgerAccountService;
use Illuminate\Http\Client\Request as ClientRequest;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class LedgerController extends SmartController
{
    use HasMVConnector;

    private $connectorService;

    public function __construct(Request $request, LedgerAccountService $ledgerService)
    {
        parent::__construct($request);
        $this->connectorService = $ledgerService;
    }

    public function index(Request $request)
    {
        $accounts = $this->connectorService->index(
            $request->input('search'),
            $request->input('cashorbank')
        );
        return response()->json([
            'success' => true,
            'accounts' => $accounts
        ]);
    }

    public function show($id)
    {
        try {
            $result = $this->connectorService->findOrFail($id);
            return response()->json(
                [
                    'success' => true,
                    'data' => $result
                ]
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'success' => false,
                    'data' => null
                ]
            );
        }
    }

    // public function cashBankAccounts()
    // {
    //     try {
    //         $result = $this->ledgerService->accountsByType(['cash', 'bank']);
    //         return response()->json(
    //             [
    //                 'success' => true,
    //                 'data' => $result
    //             ]
    //         );
    //     } catch (\Throwable $e) {
    //         return response()->json(
    //             [
    //                 'success' => false,
    //                 'data' => null
    //             ]
    //         );
    //     }
    // }

    public function store(StoreLedgerAccountRequest $request)
    {
        $district_id = $request->input('district_id') ?: auth()->user()->district;

        if (
            Gate::denies(
            'create',
            [
                LedgerAccount::class,
                $district_id
            ])
            ) {
            throw new UnauthorizedException('Unauthorized action', 404);
        }
        try {
            $data = $request->validated();
            $data['district_id'] = $district_id;
            $result = $this->connectorService->create(
                $data
            );
            return response()->json(
                [
                    'success' => true,
                    'data' => $result
                ]
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'success' => false,
                    'error' => $e->getMessage()
                ]
            );
        }
    }

    public function update($id, UpdateLedgerAccountRequest $request)
    {
        try {
            $result = $this->connectorService->update($request->validated(), $id);
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'success' => false,
                    'error' => $e->getMessage()
                ]
            );
        }

        return response()->json(
            [
                'success' => $result
            ]
        );
    }

    public function delete($id)
    {
        $result = $this->connectorService->delete($id);
        return response()->json(
            [
                'success' => (bool)$result
            ]
        );
    }
}
