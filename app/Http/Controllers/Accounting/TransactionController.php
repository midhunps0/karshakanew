<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use App\Models\Accounting\LedgerAccount;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use Spatie\FlareClient\Http\Exceptions\InvalidData;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class TransactionController extends SmartController
{
    private $transactionService;

    public function __construct(Request $request, TransactionService $service)
    {
        parent::__construct($request);
        $this->transactionService = $service;
    }

    public function index(Request $request)
    {
        try {
            $result = $this->transactionService->getTransactions(
                $request->input('from'),
                $request->input('to'),
            );

            return $this->buildResponse(
                'admin.accounts.transactions.journal_report',
                [
                    'success' => true,
                    'transactions' => $result
                ]
            );
        } catch (InvalidData $e) {
            return $this->buildResponse(
                'admin.accounts.transactions.journal_report',
                [
                    'success' => false,
                    'error' => 'Invalid date format',
                    'transactions' => []
                ]
            );
        } catch (\Throwable $e) {
            return $this->buildResponse(
                'admin.accounts.transactions.journal_report',
                [
                    'success' => true,
                    'error' => 'Unexpected error.',
                    'transactions' => []
                ]
            );
        }
    }

    public function show($id)
    {
        try {
            $result = $this->transactionService->findOrFail($id);
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

    public function create()
    {
        return $this->buildResponse('admin.accounts.transactions.create', []);
    }

    public function createJournal()
    {
        $accounts = LedgerAccount::userDistrictConstrained()->get();
        return $this->buildResponse('admin.accounts.transactions.create_journal', [
            'accounts' => $accounts,
        ]);
    }

    public function createReceipt()
    {
        $accounts = LedgerAccount::userDistrictConstrained()->get();
        return $this->buildResponse('admin.accounts.transactions.create_receipt', [
            'accounts' => $accounts,
        ]);
    }

    public function createPayment()
    {
        $accounts = LedgerAccount::userDistrictConstrained()->get();
        return $this->buildResponse('admin.accounts.transactions.create_voucher', [
            'accounts' => $accounts,
        ]);
    }

    public function store(StoreTransactionRequest $request)
    {
        try {
            $result = $this->transactionService->store(
                $request->validated()
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

    public function update($id, UpdateTransactionRequest $request)
    {
        try {
            $result = $this->transactionService->update(
                $request->validated(),
                $id
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

    public function delete($id)
    {
        $result = $this->transactionService->delete($id);
        return response()->json(
            [
                'success' => $result
            ]
        );
    }
}
