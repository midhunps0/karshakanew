<?php
namespace Database\Seeders;

use App\Models\Accounting\AccountGroup;
use App\Models\District;
use Illuminate\Database\Seeder;

class AccountGroupSeeder extends Seeder
{
    private $coreGroups = [
        'Assets' => [
            'Fixed Assets',
            'Current Assets'
        ],
        'Liabilities' => [
            'Long Term Liabilities',
            'Current Liabilities'
        ],
        'Incomes' => [
            'Direct Incomes',
            'Indirect Incomes'
        ],
        'Expenses' => [
            'Direct Expenses',
            'Indirect Expenses'
        ]
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->coreGroups as $group => $subGroups) {
            $districtId = District::where('name', 'Ernakulam')->get()->first()->id;
            $this->createGroup($group, $subGroups, $districtId);
            $districtId = District::where('name', 'Kottayam')->get()->first()->id;
            $this->createGroup($group, $subGroups, $districtId);
        }
    }

    private function createGroup($group, $subGroups, $districtId)
    {
        $parent = AccountGroup::create(
            [
                'district_id' => $districtId,
                'name' => $group,
                'is_core_group' => true
            ]
        );
        foreach ($subGroups as $sub) {
            AccountGroup::create(
                [
                    'district_id' => $districtId,
                    'name' => $sub,
                    'parent_id' => $parent->id,
                    'is_core_group' => true
                ]
            );
        }
    }
}
