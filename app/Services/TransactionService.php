<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Helpers\AppHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Models\Accounting\Transaction;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accounting\TransactionClient;
use Spatie\FlareClient\Http\Exceptions\InvalidData;

class TransactionService
{
    public function model()
    {
        return Transaction::class;
    }

    public function getTransactions($from, $to)
    {
        $from = str_replace("/", "-", $from);
        $to = str_replace("/", "-", $to);
        $f = explode('-', $from);
        $t = explode('-', $to);
        if (!checkdate($f[1], $f[0], $f[2]) || !checkdate($t[1], $t[0], $t[2])) {
            throw new InvalidData('Incorrect date format', 400);
        }
        // return $from . ' : ' . $to;
        return Transaction::where('date', '>=', $f[2].'-'.$f[1].'-'.$f[0])
            ->where('date', '<=', $t[2].'-'.$t[1].'-'.$t[0])
            ->with(['clients'])
            ->orderBy('created_at')
            ->get();
    }

    public function findOrFail($id)
    {
        return Transaction::findOrFail($id)
            ->with('debtors', 'creditors')
            ->get()->first();
    }

    public function store($inputs)
    {
        if (!isset($inputs['district_id'])) {
            $inputs['district_id'] = auth()->user()->district_id;
        }
        // if (Gate::denies('create', [Transaction::class, $inputs['district_id']])) {
        //     return response()->json(['error' => 'Unauthorized action.'], 404);
        // }
        $clients = $this->processClients($inputs['clients']);
        if ($clients == null) {
            throw new Exception('Creditors & debtors totals mismatch.');
        }
        unset($inputs['clients']);
        $inputs['date'] = AppHelper::formatDateForSave($inputs['date']);
        $inputs['amount'] = $clients['amount'];
        $inputs['owner_id'] = auth()->user()->id;
        $transaction = null;
        DB::transaction(
            function () use ($clients, $inputs, &$transaction) {

                Transaction::lockForUpdate();

                // if (!isset($inputs['ref_no'])) {
                //     $inputs['ref_no'] = $this->getRefDocNumber(
                //         $inputs['type'],
                //         $inputs['district_id']
                //     );
                // }

                $transaction = Transaction::create($inputs);

                foreach ($clients['debtors'] as $debtor) {
                    $this->createTransactionClient($transaction->id, $debtor, 'debit');
                }

                foreach ($clients['creditors'] as $creditor) {
                    $this->createTransactionClient($transaction->id, $creditor, 'credit');
                }
            }
        );

        return Transaction::find($transaction->id);
    }

    public function update($inputs, $id, $attribute = 'id')
    {
        $clients = $this->processClients($inputs['clients']);
        if (!$clients) {
            throw new Exception('Creditors & debtors totals mismatch.');
        }

        $transaction = Transaction::find($id);

        if (Gate::denies('update', [Transaction::class, $transaction])) {
            return response()->json(['error' => 'Unauthorized action.'], 404);
        }

        DB::transaction(
            function () use ($clients, $inputs, &$transaction) {

                Transaction::lockForUpdate();

                // if (!isset($inputs['ref_no'])) {
                //     $inputs['ref_no'] = $this->getRefDocNumber(
                //         $inputs['type'],
                //         $inputs['district_id']
                //     );
                // }

                $inputs['amount'] = $clients['amount'];
                $transaction->clients()->forceDelete();
                $transaction->update($inputs);

                // if (isset($inputs['date'])) {
                //     $transaction->date = $inputs['date'];
                // }
                // $transaction->amount = $clients['amount'];
                // if (isset($inputs['type'])) {
                //     $transaction->type = $inputs['type'];
                // }
                // if (isset($inputs['ref_no'])) {
                //     $transaction->ref_no = $inputs['ref_no'];
                // }
                // if (isset($inputs['remarks'])) {
                //     $transaction->remarks = $inputs['remarks'];
                // }
                // $transaction->owner_id = auth('api')->id();
                // $transaction->save();

                foreach ($clients['debtors'] as $debtor) {
                    $this->createTransactionClient($transaction->id, $debtor, 'debit');
                }

                foreach ($clients['creditors'] as $creditor) {
                    $this->createTransactionClient($transaction->id, $creditor, 'credit');
                }
            }
        );

        return Transaction::find($transaction->id);
    }

    public function delete($id)
    {
        try {
            DB::transaction(
                function () use ($id) {
                    TransactionClient::where('transaction_id', $id)->delete();
                    Transaction::destroy($id);
                }
            );
        } catch (\Throwable $e) {
            return false;
        }
        return true;
    }

    private function processClients($clients)
    {
        $debtors = [];
        $creditors = [];
        $dsum = 0;
        $csum = 0;
        foreach ($clients as $client) {
            if ($client['action'] == 'debit') {
                $debtors[] = $client;
                $dsum += $client['amount'];
            } elseif ($client['action'] == 'credit') {
                $creditors[] = $client;
                $csum += $client['amount'];
            }
        }
        if ($dsum != $csum) {
            return null;
        }
        return [
            'debtors' => $debtors,
            'creditors' => $creditors,
            'amount' => $dsum
        ];
    }

    private function createTransactionClient($transaction_id, $client, $action)
    {
        TransactionClient::create(
            [
                'transaction_id' => $transaction_id,
                'ledger_account_id' => $client['account_id'],
                'client_amount' => $client['amount'],
                'action' => $action
            ]
        );
    }
    private function getRefDocNumber(string $type, int $district_id): string
    {
        // $t = Transaction::where('type', $type)
        //     ->where('district_id', $district_id)
        //     ->select('ref_no')
        //     ->latest()
        //     ->first();

        return '';

    }
}
