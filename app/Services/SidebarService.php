<?php
namespace App\Services;

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
                    // [
                    //     'type' => 'menu_item',
                    //     'title' => 'Roles',
                    //     'route' => 'roles.index',
                    //     'route_params' => [],
                    //     'icon' => 'easyadmin::icons.users',
                    //     'show' => $this->showRoles()
                    // ],
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
                    // [
                    //     'type' => 'menu_item',
                    //     'title' => 'All Trade Unions',
                    //     'route' => 'tradeunions.index',
                    //     'route_params' => [],
                    //     'icon' => 'easyadmin::icons.users',
                    //     'show' => (new TradeUnionService())->authoriseIndex()
                    // ],
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
    private function showPermissions()
    {
        return auth()->check();
    }
    // private function showTradeUnions()
    // {
    //     return (new TradeUnionService())->authoriseIndex();
    // }
}
?>
