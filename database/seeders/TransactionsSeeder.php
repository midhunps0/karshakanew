<?php
namespace Database\Seeders;

use App\Helpers\AppHelper;
use App\Models\District;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use App\Models\Accounting\Transaction;
use App\Models\Accounting\LedgerAccount;
use App\Models\Accounting\TransactionClient;

class TransactionsSeeder extends Seeder
{
    private $districts; // populate this with db data from districts seeder

    private $users; //populate this with db users data created by users seeder
    // ekm_admin, ekm_exe, alp_admin, alp_exe, state_admin

    private $transactions = [
        [
            'district' => 'Ernakulam',
            'date' => '01/01/2022',
            'type' => 'receipt',
            'owner' => 'ekm_admin',
            'amount' => 25000,
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 25000,
                    'action' => 'debit'
                ],
                [
                    'ledger_account' => 'Sales',
                    'client_amount' => 25000,
                    'action' => 'credit'
                ],
            ]
        ],
        [
            'district' => 'Ernakulam',
            'date' => '02/01/2022',
            'amount' => 1200,
            'type' => 'receipt',
            'owner' => 'ekm_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 1200,
                    'action' => 'credit'
                ],
                [
                    'ledger_account' => 'Conveyance Account',
                    'client_amount' => 1200,
                    'action' => 'debit'
                ],
            ]
        ],
        [
            'district' => 'Ernakulam',
            'date' => '03/01/2022',
            'amount' => 1600,
            'type' => 'payment',
            'owner' => 'ekm_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 1600,
                    'action' => 'credit'
                ],
                [
                    'ledger_account' => 'Purchases',
                    'client_amount' => 1600,
                    'action' => 'debit'
                ],
            ]
        ],
        [
            'district' => 'Ernakulam',
            'date' => '04/01/2022',
            'amount' => 5000,
            'type' => 'receipt',
            'owner' => 'ekm_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 5000,
                    'action' => 'debit'
                ],
                [
                    'ledger_account' => 'Rent Received',
                    'client_amount' => 5000,
                    'action' => 'credit'
                ],
            ]
        ],
        [
            'district' => 'Ernakulam',
            'date' => '05/01/2022',
            'amount' => 22000,
            'type' => 'payment',
            'owner' => 'ekm_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 22000,
                    'action' => 'credit'
                ],
                [
                    'ledger_account' => 'Raj Salary',
                    'client_amount' => 22000,
                    'action' => 'debit'
                ],
            ]
        ],
        [
            'district' => 'Ernakulam',
            'date' => '06/01/2022',
            'amount' => 900,
            'type' => 'payment',
            'owner' => 'ekm_exe',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 900,
                    'action' => 'credit'
                ],
                [
                    'ledger_account' => 'Maintenance & Repair',
                    'client_amount' => 900,
                    'action' => 'debit'
                ],
            ]
        ],
        [
            'district' => 'Ernakulam',
            'date' => '07/01/2022',
            'amount' => 5000,
            'type' => 'journal',
            'owner' => 'ekm_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 5000,
                    'action' => 'credit'
                ],
                [
                    'ledger_account' => 'Bank (KGB)',
                    'client_amount' => 5000,
                    'action' => 'debit'
                ],
            ]
        ],
        [
            'district' => 'Ernakulam',
            'date' => '08/01/2022',
            'amount' => 1980,
            'type' => 'payment',
            'owner' => 'ekm_exe',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 1980,
                    'action' => 'credit'
                ],
                [
                    'ledger_account' => 'Furniture',
                    'client_amount' => 1980,
                    'action' => 'debit'
                ],
            ]
        ],
        [
            'district' => 'Ernakulam',
            'date' => '09/01/2022',
            'amount' => 10000,
            'type' => 'receipt',
            'owner' => 'ekm_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 10000,
                    'action' => 'debit'
                ],
                [
                    'ledger_account' => 'Equity',
                    'client_amount' => 10000,
                    'action' => 'credit'
                ],
            ]
        ],
        [
            'district' => 'Ernakulam',
            'date' => '10/01/2022',
            'amount' => 8000,
            'type' => 'receipt',
            'owner' => 'ekm_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 8000,
                    'action' => 'debit'
                ],
                [
                    'ledger_account' => 'Advances Received',
                    'client_amount' => 8000,
                    'action' => 'credit'
                ],
            ]
        ],
        [
            'district' => 'Ernakulam',
            'date' => '11/01/2022',
            'amount' => 1120,
            'type' => 'receipt',
            'owner' => 'ekm_exe',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 1120,
                    'action' => 'debit'
                ],
                [
                    'ledger_account' => 'Membership Fees',
                    'client_amount' => 1000,
                    'action' => 'credit'
                ],
                [
                    'ledger_account' => 'Fine',
                    'client_amount' => 120,
                    'action' => 'credit'
                ],
            ]
        ],
        [
            'district' => 'Alapuzha',
            'date' => '01/01/2022',
            'amount' => 30000,
            'type' => 'receipt',
            'owner' => 'alp_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 30000,
                    'action' => 'debit'
                ],
                [
                    'ledger_account' => 'Sales',
                    'client_amount' => 30000,
                    'action' => 'credit'
                ],
            ]
        ],
        [
            'district' => 'Alapuzha',
            'date' => '02/01/2022',
            'amount' => 1800,
            'type' => 'receipt',
            'owner' => 'alp_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 1800,
                    'action' => 'credit'
                ],
                [
                    'ledger_account' => 'Conveyance Account',
                    'client_amount' => 1800,
                    'action' => 'debit'
                ],
            ]
        ],
        [
            'district' => 'Alapuzha',
            'date' => '03/01/2022',
            'amount' => 1100,
            'type' => 'payment',
            'owner' => 'alp_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 1100,
                    'action' => 'credit'
                ],
                [
                    'ledger_account' => 'Purchases',
                    'client_amount' => 1100,
                    'action' => 'debit'
                ],
            ]
        ],
        [
            'district' => 'Alapuzha',
            'date' => '04/01/2022',
            'amount' => 3000,
            'type' => 'receipt',
            'owner' => 'alp_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 3000,
                    'action' => 'debit'
                ],
                [
                    'ledger_account' => 'Rent Received',
                    'client_amount' => 3000,
                    'action' => 'credit'
                ],
            ]
        ],
        [
            'district' => 'Alapuzha',
            'date' => '05/01/2022',
            'amount' => 14000,
            'type' => 'payment',
            'owner' => 'alp_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 14000,
                    'action' => 'credit'
                ],
                [
                    'ledger_account' => 'Raj Salary',
                    'client_amount' => 14000,
                    'action' => 'debit'
                ],
            ]
        ],
        [
            'district' => 'Alapuzha',
            'date' => '06/01/2022',
            'amount' => 500,
            'type' => 'payment',
            'owner' => 'alp_exe',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 500,
                    'action' => 'credit'
                ],
                [
                    'ledger_account' => 'Maintenance & Repair',
                    'client_amount' => 500,
                    'action' => 'debit'
                ],
            ]
        ],
        [
            'district' => 'Alapuzha',
            'date' => '07/01/2022',
            'amount' => 6000,
            'type' => 'journal',
            'owner' => 'alp_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 6000,
                    'action' => 'credit'
                ],
                [
                    'ledger_account' => 'Bank (KGB)',
                    'client_amount' => 6000,
                    'action' => 'debit'
                ],
            ]
        ],
        [
            'district' => 'Alapuzha',
            'date' => '08/01/2022',
            'amount' => 1950,
            'type' => 'payment',
            'owner' => 'alp_exe',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 1950,
                    'action' => 'credit'
                ],
                [
                    'ledger_account' => 'Furniture',
                    'client_amount' => 1950,
                    'action' => 'debit'
                ],
            ]
        ],
        [
            'district' => 'Alapuzha',
            'date' => '09/01/2022',
            'amount' => 12000,
            'type' => 'receipt',
            'owner' => 'alp_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 12000,
                    'action' => 'debit'
                ],
                [
                    'ledger_account' => 'Equity',
                    'client_amount' => 12000,
                    'action' => 'credit'
                ],
            ]
        ],
        [
            'district' => 'Alapuzha',
            'date' => '10/01/2022',
            'amount' => 11000,
            'type' => 'receipt',
            'owner' => 'alp_admin',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 11000,
                    'action' => 'debit'
                ],
                [
                    'ledger_account' => 'Advances Received',
                    'client_amount' => 11000,
                    'action' => 'credit'
                ],
            ]
        ],
        [
            'district' => 'Alapuzha',
            'date' => '11/01/2022',
            'amount' => 1120,
            'type' => 'receipt',
            'owner' => 'alp_exe',
            'clients' => [
                [
                    'ledger_account' => 'Cash',
                    'client_amount' => 1120,
                    'action' => 'debit'
                ],
                [
                    'ledger_account' => 'Membership Fees',
                    'client_amount' => 900,
                    'action' => 'credit'
                ],
                [
                    'ledger_account' => 'Fine',
                    'client_amount' => 220,
                    'action' => 'credit'
                ],
            ]
        ],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->transactions as $transaction) {
            $date = AppHelper::dateFromString($transaction['date']);
            $t = factory(Transaction::class)->create(
                [
                    'district_id' => District::where('name', $transaction['district'])->get()->first()->id,
                    'date' => $date,
                    'amount' => $transaction['amount'],
                    'type' => $transaction['type'],
                ]
            );
            foreach ($transaction['clients'] as $tc) {
                factory(TransactionClient::class)->create(
                    [
                        'transaction_id' => $t->id,
                        'ledger_account_id' => $this->findAccountByNameAndDistrict(
                            $tc['ledger_account'],
                            $transaction['district']
                        ),
                        'client_amount' => $tc['client_amount'],
                        'action' => $tc['action']
                    ]
                );
            }
        }
    }

    private function findAccountByNameAndDistrict($name, $district)
    {
        $districtId = District::where('name', $district)->get()->first()->id;
        return LedgerAccount::where('name', $name)->where('district_id', $districtId)
            ->get()->first()->id;
    }
}
