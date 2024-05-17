<?php
namespace App\Services;

use App\Models\Allowance;
use App\Models\User;
use Ynotz\EasyAdmin\Services\SidebarServiceInterface;

class SidebarService implements SidebarServiceInterface
{
    private $user;
    public function __construct()
    {
        $this->user = User::find(auth()->user()->id);
    }
    public function getSidebarData(): array
    {
        return [
            [
                'type' => 'menu_item',
                'title' => 'Dashboard',
                'route' => 'dashboard',
                'route_params' => [],
                'icon' => 'easyadmin::icons.users',
                'show' => true
            ],
            [
                'type' => 'menu_group',
                'title' => 'Members',
                'icon' => 'easyadmin::icons.gear',
                // 'show' => auth()->user()->hasPermissionTo(''),
                'show' => true,
                'menu_items' => [
                    [
                        'type' => 'menu_item',
                        'title' => 'Search Member',
                        'route' => 'members.search',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => true
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Add Member',
                        'route' => 'members.create',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => true
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Sync Member Data',
                        'route' => 'members.sync',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => true
                    ],
                ]
            ],
            [
                'type' => 'menu_group',
                'title' => 'Receipts',
                'icon' => 'easyadmin::icons.gear',
                // 'show' => auth()->user()->hasPermissionTo(''),
                'show' => true,
                'menu_items' => [
                    [
                        'type' => 'menu_item',
                        'title' => 'Search Receipts',
                        'route' => 'feecollections.search',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.search',
                        'show' => true
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Add New Receipt',
                        'route' => 'feecollections.create',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => true
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Add Receipts - Bulk',
                        'route' => 'feecollections.bulk.create',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => true
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Add Old Receipt',
                        'route' => 'feecollections.old.create',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => true
                    ],
                ]
            ],
            [
                'type' => 'menu_item',
                'title' => 'Search Applications',
                'route' => 'allowances.search',
                'route_params' => [],
                'icon' => 'easyadmin::icons.search',
                'show' => true
            ],
            [
                'type' => 'menu_item',
                'title' => 'Import Payments',
                'route' => 'allowances.bulk_payment',
                'route_params' => [],
                'icon' => 'easyadmin::icons.users',
                'show' => true
            ],
            [
                'type' => 'menu_item',
                'title' => 'Transfer Requests',
                'route' => 'members.transfer_requests',
                'route_params' => [],
                'icon' => 'easyadmin::icons.users',
                'show' => auth()->user()->hasAnyPermission([
                    'Member Transfer: Edit In Any District',
                    'Member Transfer: Edit In Own District',
                ])
            ],
            // [
            //     'type' => 'menu_group',
            //     'title' => 'Applications',
            //     'icon' => 'easyadmin::icons.users',
            //     'show' => $this->showAccessControl(),
            //     'menu_items' => [
            //         [
            //             'type' => 'menu_item',
            //             'title' => 'Education Allowance',
            //             'route' => 'allowances.education.create',
            //             'route_params' => [],
            //             'icon' => 'easyadmin::icons.users',
            //             'show' => $this->showAccessControl()
            //         ],
            //     ]
            // ],
            [
                'type' => 'menu_group',
                'title' => 'Member Reports',
                'icon' => 'easyadmin::icons.gear',
                'show' => $this->showReports(),
                'menu_items' => [
                    [
                        'type' => 'menu_item',
                        'title' => 'Gender-wise',
                        'route' => 'members.report.gender',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showMembersReport()
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'New Registrations',
                        'route' => 'members.report.new',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showMembersReport()
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Active/inactive',
                        'route' => 'members.report.status',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showMembersReport()
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Custom Report',
                        'route' => 'members.report.custom',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showMembersReport()
                    ],
                ],
            ],
            [
                'type' => 'menu_group',
                'title' => 'Other Reports',
                'icon' => 'easyadmin::icons.gear',
                'show' => $this->showReports(),
                'menu_items' => [
                    [
                        'type' => 'menu_item',
                        'title' => 'Collections',
                        'route' => 'feecollections.report',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showCollections()
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Allowances',
                        'route' => 'allowances.report',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showAllowancesReport()
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Monthly Snapshot',
                        'route' => 'snapshot.report',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showAllowancesReport()
                    ],
                ],
            ],
            [
                'type' => 'menu_section',
                'title' => 'Accounts',
                'icon' => 'easyadmin::icons.users',
            ],
            [
                'type' => 'menu_group',
                'title' => 'Transactions',
                'icon' => 'easyadmin::icons.gear',
                // 'show' => auth()->user()->hasPermissionTo(''),
                'show' => true,
                'menu_items' => [
                    [
                        'type' => 'menu_item',
                        'title' => 'Create Journal Entry',
                        'route' => 'transaction.create.journal',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showTransaction()
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Create Receipt',
                        'route' => 'transaction.create.receipt',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showTransaction()
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Create Voucher',
                        'route' => 'transaction.create.voucher',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showTransaction()
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Journal Entries',
                        'route' => 'transaction.index',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showJournal()
                    ],
                ]
            ],
            [
                'type' => 'menu_group',
                'title' => 'Ledger Accounts',
                'icon' => 'easyadmin::icons.gear',
                // 'show' => auth()->user()->hasPermissionTo(''),
                'show' => true,
                'menu_items' => [
                    [
                        'type' => 'menu_item',
                        'title' => 'Chart Of Accounts',
                        'route' => 'accounts.chart',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showJournal()
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Account Statement',
                        'route' => 'accounts.account.statement',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showJournal()
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Add Ledger Account',
                        'route' => 'ledgeraccounts.create',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->createLedgerPermission()
                    ],
                ]
            ],
            [
                'type' => 'menu_group',
                'title' => 'Account Groups',
                'icon' => 'easyadmin::icons.gear',
                // 'show' => auth()->user()->hasPermissionTo(''),
                'show' => true,
                'menu_items' => [
                    [
                        'type' => 'menu_item',
                        'title' => 'Account Groups',
                        'route' => 'accountgroups.index',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => true
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Add Account Group',
                        'route' => 'accountgroups.create',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => true
                    ],
                ]
            ],
            [
                'type' => 'menu_section',
                'title' => 'Settings',
                'icon' => 'easyadmin::icons.gear',
            ],
            [
                'type' => 'menu_group',
                'title' => 'Access Control',
                'icon' => 'easyadmin::icons.users',
                'show' => $this->showAccessControl(),
                'menu_items' => [
                    [
                        'type' => 'menu_item',
                        'title' => 'Users',
                        'route' => 'users.index',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showAccessControl()
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Roles - permissions',
                        'route' => 'roles.index',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->showRoles()
                    ],
                    // [
                    //     'type' => 'menu_item',
                    //     'title' => 'Permissions',
                    //     'route' => 'permissions.index',
                    //     'route_params' => [],
                    //     'icon' => 'easyadmin::icons.users',
                    //     'show' => $this->showPermissions()
                    // ],
                ]
            ],
            [
                'type' => 'menu_group',
                'title' => 'App Settings',
                'icon' => 'easyadmin::icons.gear',
                'show' => $this->showRoles(),
                'menu_items' => [
                    [
                        'type' => 'menu_item',
                        'title' => 'Districts',
                        'route' => 'districts.index',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->manageDistrictsPermission()
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Taluks',
                        'route' => 'taluks.index',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->manageTaluksPermission()
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Villages',
                        'route' => 'villages.index',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->manageVillagesPermission()
                    ],
                    [
                        'type' => 'menu_item',
                        'title' => 'Welfare Schemes',
                        'route' => 'welfareschemes.index',
                        'route_params' => [],
                        'icon' => 'easyadmin::icons.users',
                        'show' => $this->manageSchemesPermission()
                    ],
                    // [
                    //     'type' => 'menu_item',
                    //     'title' => 'All Trade Unions',
                    //     'route' => 'tradeunions.index',
                    //     'route_params' => [],
                    //     'icon' => 'easyadmin::icons.users',
                    //     'show' => (new TradeUnionService())->authoriseIndex()
                    // ],
                    // [
                    //     'type' => 'menu_item',
                    //     'title' => 'Add Trade Union',
                    //     'route' => 'tradeunions.create',
                    //     'route_params' => [],
                    //     'icon' => 'easyadmin::icons.users',
                    //     'show' => (new TradeUnionService())->authoriseCreate()
                    // ],
                ]
            ],

            // [
            //     'type' => 'menu_section',
            //     'title' => 'Menu Group',
            //     'icon' => 'easyadmin::icons.gear',
            //     'show' => $this->showRoles(),
            //     'menu_items' => [
            //         [
            //             'type' => 'menu_item',
            //             'title' => 'Menu Item Two',
            //             'route' => 'home',
            //             'route_params' => [],
            //             'icon' => 'easyadmin::icons.plus',
            //             'show' => $this->showRoles()
            //         ],
            //     ]
            // ],
            // [
            //     'type' => 'menu_item',
            //     'title' => 'Menu Item Two',
            //     'route' => 'home',
            //     'route_params' => [],
            //     'icon' => 'easyadmin::icons.plus',
            //     'show' => $this->showRoles()
            // ],
        ];
    }
    private function showAccessControl($action = 'View')
    {
       return $this->user->hasPermissionTo("User: {$action} In Any District") ||
        $this->user->hasPermissionTo("User: {$action} In Own District");
    }
    private function showRoles()
    {
        return $this->user->hasPermissionTo("Role: Edit In Any District") ||
        $this->user->hasPermissionTo("Role: Edit In Own District");
    }
    private function showCollections()
    {
        return auth()->check();
    }
    private function showAllowancesReport()
    {
        return auth()->user()->can('viewReport', Allowance::class);
    }
    private function showReports()
    {
        return $this->showCollections() ||
            $this->showAllowancesReport();
    }
    private function showPermissions()
    {
        return auth()->check();
    }
    private function showJournal()
    {
        return auth()->user()->hasPermissionTo('Journal: View In Any District') || auth()->user()->hasPermissionTo('Journal: View In Own District');
    }
    private function showTransaction()
    {
        return auth()->user()->hasPermissionTo('Journal: Create In Any District') || auth()->user()->hasPermissionTo('Journal: Create In Own District');
    }
    private function createLedgerPermission()
    {
        return auth()->user()->hasPermissionTo('Ledger Account: Create In Any District') || auth()->user()->hasPermissionTo('Ledger Account: Create In Own District');
    }
    // private function showTradeUnions()
    // {
    //     return (new TradeUnionService())->authoriseIndex();
    // }
    public function manageDistrictsPermission()
    {
        return auth()->user()->hasPermissionTo('District: Create');
    }
    public function manageTaluksPermission()
    {
        return auth()->user()->hasPermissionTo('Taluk: Create In Any District') || auth()->user()->hasPermissionTo('Taluk: Create In Own District');
    }
    public function manageVillagesPermission()
    {
        return auth()->user()->hasPermissionTo('Village: Create In Any District') || auth()->user()->hasPermissionTo('Village: Create In Own District');
    }
    public function manageSchemesPermission()
    {
        return auth()->user()->hasPermissionTo('Welfare Scheme: View');
    }
    public function showMembersReport()
    {
        return auth()->user()->hasPermissionTo('Member: Edit In Any District')
            || auth()->user()->hasPermissionTo('Member: Edit In Own District');
    }
}
?>
