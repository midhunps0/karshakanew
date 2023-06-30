<?php
namespace Database\Seeders;

use App\Models\Accounting\AccountGroup;
use App\Models\Accounting\LedgerAccount;
use App\Models\District;
use Illuminate\Database\Seeder;

class LedgerAccountsSeeder extends Seeder
{
    private $districtId;
    private $fixedAssetsId;
    private $currentAssetsId;
    private $ltLiabilitiesId;
    private $stLiabilitiesId;
    private $dirIncomeId;
    private $dirExpId;
    private $inDirIncomeId;
    private $inDirExpId;
    private $accounts = [
        [
            'district_id' => 5,
            'name' => 'Conveyance Account',
            'description' => 'All expenses related to conveyance',
            'group_id' => 'Indirect Expenses',
            'opening_balance' => 0,
            'opening_bal_type' => 'credit',
        ],
        [
            'district_id' => 5,
            'name' => 'Fee Collections',
            'description' => 'Receipts from members',
            'group_id' => 'Direct Incomes',
            'opening_balance' => 0,
            'opening_bal_type' => 'credit',
        ],
        [
            'district_id' => 5,
            'name' => 'Purchases',
            'description' => 'Purchase Expenses',
            'group_id' => 'Direct Expenses',
            'opening_balance' => 0,
            'opening_bal_type' => 'credit',
        ],
        // [
        //     'district_id' => 5,
        //     'name' => 'Rent Received',
        //     'description' => 'Income from property rent',
        //     'group_id' => 'Indirect Incomes',
        //     'opening_balance' => 0,
        //     'opening_bal_type' => 'credit',
        // ],
        // [
        //     'district_id' => 5,
        //     'name' => 'Raj Salary',
        //     'description' => 'Salary account for Raj',
        //     'group_id' => 'Indirect Expenses',
        //     'opening_balance' => 0,
        //     'opening_bal_type' => 'credit',
        // ],
        [
            'district_id' => 5,
            'name' => 'Maintenance & Repair',
            'description' => 'Maintenance expenses for assets',
            'group_id' => 'Indirect Expenses',
            'opening_balance' => 0,
            'opening_bal_type' => 'credit',
        ],
        [
            'district_id' => 5,
            'name' => 'Bank (KGB)',
            'description' => 'Cash in bank',
            'group_id' => 'Current Assets',
            'opening_balance' => 10000,
            'opening_bal_type' => 'credit',
        ],
        [
            'district_id' => 5,
            'name' => 'Cash',
            'description' => 'Cash in hand',
            'group_id' => 'Current Assets',
            'opening_balance' => 1200,
            'opening_bal_type' => 'credit',
        ],
        [
            'district_id' => 5,
            'name' => 'Furniture',
            'description' => 'Amount spent to procure office furniture',
            'group_id' => 'Fixed Assets',
            'opening_balance' => 0,
            'opening_bal_type' => 'credit',
        ],
        // [
        //     'district_id' => 5,
        //     'name' => 'Equity',
        //     'description' => 'Promoter\'s contribution',
        //     'group_id' => 'Long Term Liabilities',
        //     'opening_balance' => 0,
        //     'opening_bal_type' => 'credit',
        // ],
        [
            'district_id' => 5,
            'name' => 'Advances Received',
            'description' => 'Short term advances',
            'group_id' => 'Current Liabilities',
            'opening_balance' => 0,
            'opening_bal_type' => 'credit',
        ],
        // [
        //     'district_id' => 5,
        //     'name' => 'Membership Fees',
        //     'description' => '',
        //     'group_id' => 'Direct Incomes',
        //     'opening_balance' => 0,
        //     'opening_bal_type' => 'credit',
        // ],
        // [
        //     'district_id' => 5,
        //     'name' => 'Fine',
        //     'description' => 'Fine collected from members',
        //     'group_id' => 'Indirect Incomes',
        //     'opening_balance' => 0,
        //     'opening_bal_type' => 'credit',
        // ],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ekm = District::where('name', 'Ernakulam')->get()->first()->id;
        $alp = District::where('name', 'Kottayam')->get()->first()->id;

        foreach ($this->accounts as $account) {
            $gid = $account['group_id'];

            $account['district_id'] = $ekm;
            $account['group_id'] = $this->findGroupId($gid, $ekm);
            LedgerAccount::create(
                $account
            );
            $account['district_id'] = $alp;
            $account['group_id'] = $this->findGroupId($gid, $alp);
            LedgerAccount::create(
                $account
            );
        }
    }

    private function findGroupId($name, $districtId)
    {
        return AccountGroup::where('name', $name)
            ->where('district_id', $districtId)->get()->first()->id;
    }
}
