<?php

namespace Tests\Unit;

use Tests\TestCase;
use AccountsTestSeeder;
use App\Helpers\AccountsHelper;
use App\Models\Accounting\AccountGroup;
use App\Models\District;
use App\Models\Accounting\LedgerAccount;
use App\Models\Accounting\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountsReportsTest extends TestCase
{
    // use RefreshDatabase;

    private static $seeded = false;

    public function setUp(): void {
        parent::setUp();
        if (!self::$seeded) {
            $this->artisan('migrate:fresh');
            $this->seed(AccountsTestSeeder::class);
            self::$seeded = true;
        }
    }

    public function test_ledger_account_balance()
    {
        $districtId = District::where('name', 'Ernakulam')->get()->first()->id;
        $accountId = LedgerAccount::where('name', 'Cash')->where('district_id', $districtId)->get()->first()->id;
        $ach = new AccountsHelper();
        $bal = $ach->getAccountsBalance([$accountId]);

        $this->assertEquals(15240, $bal->amount);
        $this->assertEquals(-15240, $bal->netCredit);
        $this->assertEquals('debit', $bal->type);
    }

    public function test_ledger_account_balance_on_date()
    {
        $districtId = District::where('name', 'Ernakulam')->get()->first()->id;
        $accountId = LedgerAccount::where('name', 'Cash')->where('district_id', $districtId)->get()->first()->id;
        $ach = new AccountsHelper();
        $bal = $ach->getAccountsBalance([$accountId], '05/01/2022');

        $this->assertEquals(4000, $bal->amount);
        $this->assertEquals(-4000, $bal->netCredit);
        $this->assertEquals('debit', $bal->type);
    }

    public function test_group_balance() {
        $districtId = District::where('name', 'Ernakulam')->get()->first()->id;
        $groupId = AccountGroup::where('name', 'Current Assets')->where('district_id', $districtId)->get()->first()->id;

        $cashAcId = LedgerAccount::where('name', 'Cash')->where('district_id', $districtId)->get()->first()->id;
        $bankAcId = LedgerAccount::where('name', 'Bank (KGB)')->where('district_id', $districtId)->get()->first()->id;

        $ach = new AccountsHelper();
        $bal = $ach->getAccountsBalance([$cashAcId, $bankAcId]);
        $groupBal = $ach->getGroupBalance($groupId);

        $this->assertEquals($bal->netCredit, $groupBal->netCredit);
        $this->assertEquals($bal->amount, $groupBal->amount);
        $this->assertEquals($bal->type, $groupBal->type);


        $balDt = $ach->getAccountsBalance([$cashAcId, $bankAcId], '05/01/2022');
        $groupBalDt = $ach->getGroupBalance($groupId, '05/01/2022');

        $this->assertEquals($balDt->netCredit, $groupBalDt->netCredit);
        $this->assertEquals($balDt->amount, $groupBalDt->amount);
        $this->assertEquals($balDt->type, $groupBalDt->type);
    }

    public function test_account_statement()
    {
        $districtId = District::where('name', 'Ernakulam')->get()->first()->id;
        $cashAcId = LedgerAccount::where('name', 'Cash')->where('district_id', $districtId)->get()->first()->id;
        $acc = LedgerAccount::find($cashAcId);

        $ach = new AccountsHelper();

        $stmt = $ach->accountStatement($cashAcId);

        $this->assertIsArray($stmt);

        $this->assertArrayHasKey('opening', $stmt);
        $this->assertArrayHasKey('transactions', $stmt);
        $this->assertArrayHasKey('closing', $stmt);

        $this->arrayHasKey('date', $stmt['opening']);
        $this->arrayHasKey('description', $stmt['opening']);
        $this->arrayHasKey('amount', $stmt['opening']);
        $this->arrayHasKey('account_action', $stmt['opening']);
        $this->arrayHasKey('net_balance', $stmt['opening']);

        $this->assertEquals(11, count($stmt['transactions']));

        $this->arrayHasKey('date', $stmt['transactions'][1]);
        $this->arrayHasKey('transaction_id', $stmt['transactions'][1]);
        $this->arrayHasKey('opposite_clients', $stmt['transactions'][1]);

        $this->arrayHasKey('transaction_id', $stmt['transactions'][1]['opposite_clients']);
        $this->arrayHasKey('opposite_client_id', $stmt['transactions'][1]['opposite_clients']);
        $this->arrayHasKey('opposite_client_name', $stmt['transactions'][1]['opposite_clients']);
        $this->arrayHasKey('description', $stmt['transactions'][1]['opposite_clients']);
        $this->arrayHasKey('amount', $stmt['transactions'][1]['opposite_clients']);
        $this->arrayHasKey('account_action', $stmt['transactions'][1]['opposite_clients']);
        $this->arrayHasKey('net_balance', $stmt['transactions'][1]['opposite_clients']);

        $this->arrayHasKey('date', $stmt['closing']);
        $this->arrayHasKey('description', $stmt['closing']);
        $this->arrayHasKey('amount', $stmt['closing']);
        $this->arrayHasKey('account_action', $stmt['closing']);
        $this->arrayHasKey('net_balance', $stmt['closing']);

        $bal = $stmt['opening']['net_balance'];
        foreach ($stmt['transactions'] as $t) {
            foreach ($t['opposite_clients'] as $oc) {
                $bal = $oc['account_action'] == 'credit' ?
                    $bal + $oc['amount'] :
                    $bal - $oc['amount'];
                $this->assertEquals($bal, $oc['net_balance']);
            }
        }
        $this->assertEquals($bal, $stmt['closing']['net_balance']);
        $acBal = $ach->getAccountsBalance([$cashAcId]);
        $this->assertEquals($bal, $acBal->netCredit);
    }

    public function test_journal_statement()
    {
        $districtId = District::where('name', 'Ernakulam')->get()->first()->id;
        $ach = new AccountsHelper();

        //with min params
        $stmt = $ach->journalStatement($districtId);

        $this->assertIsArray($stmt);
        $this->assertEquals(11, count($stmt));
        $this->arrayHasKey('id', $stmt[0]);
        $this->arrayHasKey('type', $stmt[0]);
        $this->arrayHasKey('amount', $stmt[0]);
        $this->arrayHasKey('remarks', $stmt[0]);
        $this->arrayHasKey('ref_no', $stmt[0]);
        $this->arrayHasKey('debtors', $stmt[0]);
        $this->arrayHasKey('creditors', $stmt[0]);

        $this->assertIsArray($stmt[0]['debtors']);
        $this->assertIsArray($stmt[0]['creditors']);

        $this->arrayHasKey('ledger_account_id', $stmt[0]['debtors']);
        $this->arrayHasKey('ledger_account_name', $stmt[0]['debtors']);
        $this->arrayHasKey('client_amount', $stmt[0]['debtors']);
        $this->arrayHasKey('action', $stmt[0]['debtors']);
        $this->arrayHasKey('ledger_account_id', $stmt[0]['creditors']);
        $this->arrayHasKey('ledger_account_name', $stmt[0]['creditors']);
        $this->arrayHasKey('client_amount', $stmt[0]['creditors']);
        $this->arrayHasKey('action', $stmt[0]['creditors']);

        //with all params
        $stmtReceipts = $ach->journalStatement($districtId, 'receipt', '01/01/2022', '05/01/2022');
        $stmtPayments = $ach->journalStatement($districtId, 'payment', '01/01/2022', '05/01/2022');

        $this->assertEquals(3, count($stmtReceipts));
        $this->assertEquals(2, count($stmtPayments));
    }
}
