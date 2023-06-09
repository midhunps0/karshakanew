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
            // [
            //     'type' => 'menu_item',
            //     'title' => 'Members',
            //     'route' => 'members.index',
            //     'route_params' => [],
            //     'icon' => 'easyadmin::icons.users',
            //     'show' => true
            // ],
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
                'title' => 'Search Receipts',
                'route' => 'feecollections.search',
                'route_params' => [],
                'icon' => 'easyadmin::icons.search',
                'show' => true
            ],
            [
                'type' => 'menu_item',
                'title' => 'New Receipt',
                'route' => 'feecollections.create',
                'route_params' => [],
                'icon' => 'easyadmin::icons.users',
                'show' => true
            ],
            [
                'type' => 'menu_item',
                'title' => 'Enter Old Receipt',
                'route' => 'feecollections.old.create',
                'route_params' => [],
                'icon' => 'easyadmin::icons.users',
                'show' => true
            ],
            [
                'type' => 'menu_item',
                'title' => 'Bulk Receipts',
                'route' => 'feecollections.bulk.create',
                'route_params' => [],
                'icon' => 'easyadmin::icons.users',
                'show' => true
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
                'title' => 'Reports',
                'icon' => 'easyadmin::icons.gear',
                'show' => $this->showRoles(),
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
                ],
            ],
            [
                'type' => 'menu_group',
                'title' => 'Accounts',
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
                ]
            ],
            // [
            //     'type' => 'menu_group',
            //     'title' => 'App Settings',
            //     'icon' => 'easyadmin::icons.gear',
            //     'show' => $this->showRoles(),
            //     'menu_items' => [
            //         [
            //             'type' => 'menu_item',
            //             'title' => 'Districts',
            //             'route' => 'districts.index',
            //             'route_params' => [],
            //             'icon' => 'easyadmin::icons.users',
            //             'show' => $this->showRoles()
            //         ],
            //         [
            //             'type' => 'menu_item',
            //             'title' => 'All Trade Unions',
            //             'route' => 'tradeunions.index',
            //             'route_params' => [],
            //             'icon' => 'easyadmin::icons.users',
            //             'show' => (new TradeUnionService())->authoriseIndex()
            //         ],
            //         [
            //             'type' => 'menu_item',
            //             'title' => 'Add Trade Union',
            //             'route' => 'tradeunions.create',
            //             'route_params' => [],
            //             'icon' => 'easyadmin::icons.users',
            //             'show' => (new TradeUnionService())->authoriseCreate()
            //         ],
            //     ]
            // ],

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
       return $this->user->hasPermissionTo("User: {$action} In Any District");
    }
    private function showRoles()
    {
        return auth()->check();
    }
    private function showCollections()
    {
        return auth()->check();
    }
    private function showAllowancesReport()
    {
        return auth()->user()->can('viewReport', Allowance::class);
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
    // private function showTradeUnions()
    // {
    //     return (new TradeUnionService())->authoriseIndex();
    // }
}
?>
