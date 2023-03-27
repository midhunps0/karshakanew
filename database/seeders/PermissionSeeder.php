<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Ynotz\AccessControl\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    private $permissions = [
        'User: Create In Any District',
        'User: View In Any District',
        'User: Edit In Any District',
        'User: Delete In Any District',
        'User: Create In Own District',
        'User: View In Own District',
        'User: Edit In Own District',
        'User: Delete In Own District',
        'Role: Create In Any District',
        'Role: View In Any District',
        'Role: Edit In Any District',
        'Role: Delete In Any District',
        'Role: Create In Own District',
        'Role: View In Own District',
        'Role: Edit In Own District',
        'Role: Delete In Own District',
        'Permission: Create In Any District',
        'Permission: View In Any District',
        'Permission: Edit In Any District',
        'Permission: Delete In Any District',
        'Permission: Create In Own District',
        'Permission: View In Own District',
        'Permission: Edit In Own District',
        'Permission: Delete In Own District',
        'District: Create',
        'District: View',
        'District: Edit',
        'District: Delete',
        'Taluk: Create In Any District',
        'Taluk: View In Any District',
        'Taluk: Edit In Any District',
        'Taluk: Delete In Any District',
        'Taluk: Create In Own District',
        'Taluk: View In Own District',
        'Taluk: Edit In Own District',
        'Taluk: Delete In Own District',
        'Village: Create In Any District',
        'Village: View In Any District',
        'Village: Edit In Any District',
        'Village: Delete In Any District',
        'Village: Create In Own District',
        'Village: View In Own District',
        'Village: Edit In Own District',
        'Village: Delete In Own District',
        'Religion: Create In Any District',
        'Religion: View In Any District',
        'Religion: Edit In Any District',
        'Religion: Delete In Any District',
        'Religion: Create In Own District',
        'Religion: View In Own District',
        'Religion: Edit In Own District',
        'Religion: Delete In Own District',
        'Caste: Create In Any District',
        'Caste: View In Any District',
        'Caste: Edit In Any District',
        'Caste: Delete In Any District',
        'Caste: Create In Own District',
        'Caste: View In Own District',
        'Caste: Edit In Own District',
        'Caste: Delete In Own District',
        'Trade Union: Create In Any District',
        'Trade Union: View In Any District',
        'Trade Union: Edit In Any District',
        'Trade Union: Delete In Any District',
        'Trade Union: Create In Own District',
        'Trade Union: View In Own District',
        'Trade Union: Edit In Own District',
        'Trade Union: Delete In Own District',
        'Fee Type: Create In Any District',
        'Fee Type: View In Any District',
        'Fee Type: Edit In Any District',
        'Fee Type: Delete In Any District',
        'Fee Type: Create In Own District',
        'Fee Type: View In Own District',
        'Fee Type: Edit In Own District',
        'Fee Type: Delete In Own District',
        'Fee Collection: Create In Any District',
        'Fee Collection: View In Any District',
        'Fee Collection: Edit In Any District',
        'Fee Collection: Delete In Any District',
        'Fee Collection: Create In Own District',
        'Fee Collection: View In Own District',
        'Fee Collection: Edit In Own District',
        'Fee Collection: Delete In Own District',
        'Member: Create In Any District',
        'Member: View In Any District',
        'Member: Edit In Any District',
        'Member: Delete In Any District',
        'Member: Approve In Any District',
        'Member: Create In Own District',
        'Member: View In Own District',
        'Member: Edit In Own District',
        'Member: Delete In Own District',
        'Member: Approve In Own District',
        'Accounts Group: Create In Any District',
        'Accounts Group: View In Any District',
        'Accounts Group: Edit In Any District',
        'Accounts Group: Delete In Any District',
        'Accounts Group: Create In Own District',
        'Accounts Group: View In Own District',
        'Accounts Group: Edit In Own District',
        'Accounts Group: Delete In Own District',
        'Ledger Account: Create In Any District',
        'Ledger Account: View In Any District',
        'Ledger Account: Edit In Any District',
        'Ledger Account: Delete In Any District',
        'Ledger Account: Create In Own District',
        'Ledger Account: View In Own District',
        'Ledger Account: Edit In Own District',
        'Ledger Account: Delete In Own District',
        'Receipts: Create In Any District',
        'Receipts: View In Any District',
        'Receipts: Edit In Any District',
        'Receipts: Delete In Any District',
        'Receipts: Create In Own District',
        'Receipts: View In Own District',
        'Receipts: Edit In Own District',
        'Receipts: Delete In Own District',
        'Payments: Create In Any District',
        'Payments: View In Any District',
        'Payments: Edit In Any District',
        'Payments: Delete In Any District',
        'Payments: Create In Own District',
        'Payments: View In Own District',
        'Payments: Edit In Own District',
        'Payments: Delete In Own District',
        'Journal: Create In Any District',
        'Journal: View In Any District',
        'Journal: Edit In Any District',
        'Journal: Delete In Any District',
        'Journal: Create In Own District',
        'Journal: View In Own District',
        'Journal: Edit In Own District',
        'Journal: Delete In Own District',
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissions as $p) {
            Permission::create(
                [
                    'name' => $p,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            );
        }
    }
}
