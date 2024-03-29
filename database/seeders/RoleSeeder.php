<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ynotz\AccessControl\Models\Role;

class RoleSeeder extends Seeder
{
    private $roles = [
        'System Admin' => [
            'User: Create In Any District',
            'User: View In Any District',
            'User: Edit In Any District',
            'User: Delete In Any District',
            'Role: Create In Any District',
            'Role: View In Any District',
            'Role: Edit In Any District',
            'Role: Delete In Any District',
            'Permission: Create In Any District',
            'Permission: View In Any District',
            'Permission: Edit In Any District',
            'Permission: Delete In Any District',
            'District: Create',
            'District: View',
            'District: Edit',
            'District: Delete',
            'Taluk: Create In Any District',
            'Taluk: View In Any District',
            'Taluk: Edit In Any District',
            'Taluk: Delete In Any District',
            'Village: Create In Any District',
            'Village: View In Any District',
            'Village: Edit In Any District',
            'Village: Delete In Any District',
            'Religion: Create',
            'Religion: View',
            'Religion: Edit',
            'Religion: Delete',
            'Caste: Create',
            'Caste: View',
            'Caste: Edit',
            'Caste: Delete',
            'Trade Union: Create',
            'Trade Union: View',
            'Trade Union: Edit',
            'Trade Union: Delete',
            'Fee Type: Create',
            'Fee Type: View',
            'Fee Type: Edit',
            'Fee Type: Delete',
            'Fee Collection: Create In Any District',
            'Fee Collection: View In Any District',
            'Fee Collection: Edit In Any District',
            'Fee Collection: Delete In Any District',
            'Allowance: Create In Any District',
            'Allowance: View In Any District',
            'Allowance: Edit In Any District',
            'Allowance: Delete In Any District',
            'Allowance: Approve In Any District',
            'Allowance: Re-Approve In Any District',
            'Member: Create In Any District',
            'Member: View In Any District',
            'Member: Edit In Any District',
            'Member: Delete In Any District',
            'Member Transfer: Create In Any District',
            'Member Transfer: View In Any District',
            'Member Transfer: Edit In Any District',
            'Member Transfer: Delete In Any District',
            'Member Transfer: Approve In Any District',
            'Accounts Group: Create In Any District',
            'Accounts Group: View In Any District',
            'Accounts Group: Edit In Any District',
            'Accounts Group: Delete In Any District',
            'Ledger Account: Create In Any District',
            'Ledger Account: View In Any District',
            'Ledger Account: Edit In Any District',
            'Ledger Account: Delete In Any District',
            'Receipts: Create In Any District',
            'Receipts: View In Any District',
            'Receipts: Edit In Any District',
            'Receipts: Delete In Any District',
            'Payments: Create In Any District',
            'Payments: View In Any District',
            'Payments: Edit In Any District',
            'Payments: Delete In Any District',
            'Journal: Create In Any District',
            'Journal: View In Any District',
            'Journal: Edit In Any District',
            'Journal: Delete In Any District',
            'Allowance: View Report In Any District',
        ],
        'State Admin' => [
            'User: Create In Any District',
            'User: View In Any District',
            'User: Edit In Any District',
            'User: Delete In Any District',
            'District: View',
            'Taluk: Create In Any District',
            'Taluk: View In Any District',
            'Taluk: Edit In Any District',
            'Taluk: Delete In Any District',
            'Village: Create In Any District',
            'Village: View In Any District',
            'Village: Edit In Any District',
            'Village: Delete In Any District',
            'Religion: Create',
            'Religion: View',
            'Religion: Edit',
            'Religion: Delete',
            'Caste: Create',
            'Caste: View',
            'Caste: Edit',
            'Caste: Delete',
            'Trade Union: Create',
            'Trade Union: View',
            'Trade Union: Edit',
            'Trade Union: Delete',
            'Fee Type: Create',
            'Fee Type: View',
            'Fee Type: Edit',
            'Fee Type: Delete',
            'Fee Collection: View In Any District',
            'Member: View In Any District',
            'Allowance: View Report In Any District',
            'Accounts Group: Create In Any District',
            'Accounts Group: View In Any District',
            'Accounts Group: Edit In Any District',
            'Accounts Group: Delete In Any District',
            'Ledger Account: Create In Any District',
            'Ledger Account: View In Any District',
            'Ledger Account: Edit In Any District',
            'Receipts: Create In Any District',
            'Receipts: View In Any District',
            'Payments: Create In Any District',
            'Payments: View In Any District',
            'Journal: Create In Any District',
            'Journal: View In Any District',
        ],
        'District Admin' => [
            'User: Create In Own District',
            'User: View In Own District',
            'User: Edit In Own District',
            'User: Delete In Own District',
            'District: View',
            'Taluk: Create In Own District',
            'Taluk: View In Own District',
            'Taluk: Edit In Own District',
            'Taluk: Delete In Own District',
            'Village: Create In Own District',
            'Village: View In Own District',
            'Village: Edit In Own District',
            'Village: Delete In Own District',
            'Caste: View',
            'Member: Create In Own District',
            'Member: View In Own District',
            'Member: Edit In Own District',
            'Member: Delete In Own District',
            'Member: Approve In Own District',
            'Member Transfer: Create In Own District',
            'Member Transfer: View In Own District',
            'Member Transfer: Edit In Own District',
            'Member Transfer: Delete In Own District',
            'Member Transfer: Approve In Own District',
            'Fee Collection: Create In Own District',
            'Fee Collection: View Any In Own District',
            'Fee Collection: Edit In Own District Any Time',
            'Fee Collection: Delete In Own District',
            'Allowance: Create In Own District',
            'Allowance: View In Own District',
            'Allowance: Edit In Own District Any Time',
            'Allowance: Delete In Own District',
            'Allowance: Approve In Own District',
            'Allowance: Re-Approve In Own District',
            'Allowance: View Report In Own District',
            'Accounts Group: Create In Own District',
            'Accounts Group: View In Own District',
            'Accounts Group: Edit In Own District',
            'Accounts Group: Delete In Own District',
            'Ledger Account: Create In Own District',
            'Ledger Account: View In Own District',
            'Ledger Account: Edit In Own District',
            'Ledger Account: Delete In Own District',
            'Receipts: Create In Own District',
            'Receipts: View In Own District',
            'Receipts: Edit In Own District',
            'Receipts: Delete In Own District',
            'Payments: Create In Own District',
            'Payments: View In Own District',
            'Payments: Edit In Own District',
            'Payments: Delete In Own District',
            'Journal: Create In Own District',
            'Journal: View In Own District',
            'Journal: Edit In Own District',
            'Journal: Delete In Own District',
        ],
        'State Executive' => [
            'Religion: Create',
            'Religion: View',
            'Caste: View',
            'Accounts Group: Create In Any District',
            'Accounts Group: View In Any District',
            'Accounts Group: Edit In Any District',
            'Ledger Account: Create In Any District',
            'Ledger Account: View In Any District',
            'Ledger Account: Edit In Any District',
            'Receipts: Create In Any District',
            'Receipts: View In Any District',
            'Payments: Create In Any District',
            'Payments: View In Any District',
            'Journal: Create In Any District',
            'Journal: View In Any District',
        ],
        'District Executive' => [
            'Member: Create In Own District',
            'Member: View In Own District',
            'Fee Collection: Create In Own District',
            'Fee Collection: View Own In Own District',
            'Fee Collection: Edit In Own District Limited Time',
            'Accounts Group: Create In Own District',
            'Accounts Group: View In Own District',
            'Accounts Group: Edit In Own District',
            'Ledger Account: Create In Own District',
            'Ledger Account: View In Own District',
            'Ledger Account: Edit In Own District',
            'Receipts: Create In Own District',
            'Receipts: View In Own District',
            'Payments: Create In Own District',
            'Payments: View In Own District',
            'Journal: Create In Own District',
            'Journal: View In Own District',
        ],
        'Union Representative' => [
            'Member: Create In Own District',
            'Member: View In Own District',
        ],
        'Member' => []
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->roles as $r => $permissions) {
            $role = Role::create(
                [
                    'name' => $r
                ]
            );
            $role->assignPermissions(...$permissions);
        }
    }
}
