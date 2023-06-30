<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ynotz\AccessControl\Models\Permission;

class AccountsPermissionsSeeder extends Seeder
{
    private $permissionsStrings = [
        'ledger_account',
        'transactions',
    ];
    private $permissionActions = [
        'view',
        'create',
        'edit',
        'delete',
    ];
    private $permissionLevels = [
        'own_district',
        'any_district'
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = $this->generatePermissions();
        foreach ($permissions as $p) {
            Permission::create(
                [
                    'name' => $p
                ]
            );
        }
    }

    private function generatePermissions()
    {
        $permissions = [];
        foreach ($this->permissionsStrings as $p) {
            foreach ($this->permissionActions as $a) {
                foreach ($this->permissionLevels as $l) {
                    $permissions[] = sprintf('%s.%s.%s', $p, $a, $l);
                }
            }
        }
        return $permissions;
    }
}
