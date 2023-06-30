<?php

namespace App\Helpers;

use App\Models\Accounting\AccountGroup;
use Illuminate\Support\Carbon;
use App\Models\Accounting\Transaction;
use App\Models\Accounting\LedgerAccount;
use App\Models\Accounting\TransactionClient;
use Exception;

class AccountsHelper
{
    public function getAccountsBalance(array $accountIds, string $date = null)
    {
        $cquery = TransactionClient::forAccounts($accountIds)
            ->type('credit')->with('transaction');
        if (isset($date)) {
            $cquery->toDate($date);
        }
        $credits = $cquery->sum('client_amount') / 100;

        $dquery = TransactionClient::forAccounts($accountIds)
            ->type('debit');
        if (isset($date)) {
            $dquery->toDate($date);
        }
        $debits = $dquery->sum('client_amount') / 100;

        $accounts = LedgerAccount::find($accountIds);
        $netBal = 0;
        foreach ($accounts as $ac) {
            $netBal += $ac->openingCreditBalance();
        }

        $netBal += ($credits - $debits);

        if ($netBal >= 0) {
            $type = 'credit';
        } else {
            $type = 'debit';
        }
        return (object)[
            'netCredit' => $netBal,
            'amount' => abs($netBal),
            'type' => $type
        ];
    }

    public function accountStatement(int $accountId, string $from = null, string $to = null)
    {
        $query = TransactionClient::where('ledger_account_id', $accountId);
        if (isset($from)) {
            $query->fromDate($from);
        }
        if (isset($to)) {
            $query->toDate($to);
        }

        $tclients = $query->pluck('transaction_id')->toArray();
        $transactions = Transaction::with('clients')->whereIn('id', array_values($tclients))->get();
        $statement = [];
        if (isset($from)) {
            $openingBalance = $this->getAccountsBalance([$accountId], AppHelper::dateFromString($from)->subDay()->format('d/m/Y'));
            $obAmount = $openingBalance->amount;
            $obType = $openingBalance->type;
            $obNetBal = $openingBalance->netCredit;
        } else {
            $theAc = LedgerAccount::find($accountId);
            $obAmount = $theAc->opening_balance;
            $obType = $theAc->opening_bal_type;
            $obNetBal = $theAc->openingCreditBalance();
        }
        $bal = $obNetBal;
        $statement['opening'] = [
            'date' => $from,
            'description' => 'Opening balance',
            'amount' => $obAmount,
            'account_action' => $obType,
            'net_balance' => $obNetBal
        ];
        foreach ($transactions as $t) {
            $tdate = $t->date;
            // $tamount = $t->ammount;
            // $tremarks = $t->remarks;
            $transactionClientsData = $this->transactionClientsData($t, $accountId);
            $cType =  $transactionClientsData['account_action']; //debit or credit

            if ($cType == 'credit') {
                $desc = 'Cr. by ';
                $type = 'credit';
            } else {
                $desc = 'Dr. to ';
                $type = 'debit';
            }
            $statement['transactions'][$t->id]['date'] =  $tdate;
            $statement['transactions'][$t->id]['transaction_id'] = $t->id;

            $oppositeClients = $transactionClientsData['opposite_clients'];
            foreach ($oppositeClients as $oc) {
                $ocname = $oc->ledgerAccount->name;
                $cdesc = $desc.$ocname;
                $amt = min($transactionClientsData['own_amount'], $oc->client_amount);

                if ($type == 'credit') {
                    $bal += $amt;
                } else {
                    $bal -= $amt;
                }

                $statement['transactions'][$t->id]['opposite_clients'][] = [
                    'transaction_id' => $t->id,
                    'opposite_client_id' => $oc->id,
                    'opposite_client_name' => $ocname,
                    'description' => $cdesc,
                    'amount' => $amt,
                    'account_action' => $type,
                    'net_balance' => $bal
                ];
            }
        }

        $statement['closing'] = [
            'date' => $to,
            'description' => 'Closing balance',
            'amount' => abs($bal),
            'account_action' => $bal < 0 ? 'debit' : 'credit',
            'net_balance' => $bal
        ];
        return $statement;
    }

    public function getGroupBalance($groupId, $date = null)
    {
        $group = AccountGroup::where('id', $groupId)->with('subGroupsFamily')->get()->first();

        $accountIds = array_values($group->accounts()->pluck('id')->toArray());

        //get subGRoupFamily
        $subGroups = $group->subGroupsFamily->flatten();

        //foreach subGroup, get all accounts
        foreach ($subGroups as $sg) {
            $ids = array_values($sg->accounts()->pluck('id')->toArray());
            array_push($accountIds, ...$ids);
        }

        return $this->getAccountsBalance($accountIds, $date);
    }

    private function transactionClientsData($transaction, $clientAccId)
    {
        $data = [];
        foreach ($transaction->clients as $client) {
            if ($client->ledger_account_id == $clientAccId) {
                $data['account_action'] = $client->action;
                $data['own_amount'] = $client->client_amount;
            }
        }
        foreach ($transaction->clients as $client) {
            if($data['account_action'] != $client->action) {
                $data['opposite_clients'][] = $client;
            }
        }
        if (!isset($data['account_action'])) {
            throw new Exception("Invalid account id given for method 'transactionClientsData'");
        }
        return $data;
    }

    public function journalStatement($district_id, $type = null, $from = null, $to = null, $cashonly = null)
    {
        $tquery = Transaction::where('district_id', $district_id)
            ->with(['debtors', 'creditors']);
        if (isset($type)) {
            $tquery->where('type', $type);
        }
        if (isset($from)) {
            $tquery->fromDate($from);
        }
        if (isset($to)) {
            $tquery->toDate($to);
        }
        if (isset($cashonly) && $cashonly) {
            $tquery->whereIn('type', ['payment','receipt']);
        }
        $transactions = $tquery->get();

        $formatted = [];

        foreach ($transactions as $t) {
            $item = [];
            $item['id'] = $t->id;
            $item['type'] = $t->type;
            $item['date'] = $t->date;
            $item['amount'] = $t->amount;
            $item['remarks'] = $t->remarks;
            $item['ref_no'] = $t->ref_no;
            foreach ($t->debtors as $dr) {
                $d = [];
                $d['ledger_account_id'] = $dr->ledger_account_id;
                $d['ledger_account_name'] = $dr->ledger_account_name;
                $d['client_amount'] = $dr->client_amount;
                $d['action'] = $dr->action;
                $item['debtors'][] = $d;
            }
            foreach ($t->creditors as $cr) {
                $c = [];
                $c['ledger_account_id'] = $cr->ledger_account_id;
                $c['ledger_account_name'] = $cr->ledger_account_name;
                $c['client_amount'] = $cr->client_amount;
                $c['action'] = $cr->action;
                $item['creditors'][] = $c;
            }
            $formatted[] = $item;
        }

        return $formatted;
    }

    private function getPrimaryAccount($transaction)
    {
        # code...
    }
}