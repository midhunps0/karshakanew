<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AccountsTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DistrctDataSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            RolesPermissionsSeeder::class,
            UserSeeder::class,
            DistrctDataSeeder::class,
            AccountGroupSeeder::class,
            LedgerAccountsSeeder::class,
            TransactionsSeeder::class,
        ]);
    }
}
