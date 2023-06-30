<?php

namespace Tests\Unit;

use AccountGroupSeeder;
use App\Models\Accounting\AccountGroup;
use App\Models\Accounting\LedgerAccount;
use App\Models\District;
use App\Repository\Accounting\LedgerAccountRepository;
use DistrctDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LedgerAccountTest extends TestCase
{
    use RefreshDatabase;

    private $repo;
    private $group;
    private $district;

    public function setUp(): void {
        parent::setUp();
        $this->repo = $this->app->make(LedgerAccountRepository::class);

        $this->seed(DistrctDataSeeder::class);
        $this->seed(AccountGroupSeeder::class);
        $this->district = District::latest()->first();
        $this->group = AccountGroup::where('name', 'Indirect Expenses')->get()->first();
    }

    public function test_can_create_ledger_account_with_minimum_fields()
    {
        $account = $this->repo->create(
            [
                'district_id' => $this->district->id,
                'name' => 'Conveyance Account',
                'group_id' => $this->group->id
            ]
        );

        $this->assertNotNull($account);
    }

    public function test_can_create_ledger_account_with_all_fields()
    {
        $account = $this->repo->create(
            [
                'district_id' => $this->district->id,
                'name' => 'Conveyance Account',
                'description' => 'All expenses related to conveyance',
                'group_id' => $this->group->id,
                'opening_balance' => '10000',
                'opening_bal_type' => 'credit',
            ]
        );

        $this->assertNotNull($account);
    }
    /**
     * A basic unit test example.
     *
     * @return void
     */
    // public function testExample()
    // {
    //     $this->assertTrue(true);
    // }
}
