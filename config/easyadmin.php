<?php

use Ynotz\EasyAdmin\Services\DashboardService;
use App\Services\SidebarService;

return [
    'dashboard_sidebar' => [
        [
            'title' => 'Roles',
            'route' => 'roles.index',
            'route_params' => [],
            'icon' => 'easyadmin::icons.users'
        ]
    ],
    'dashboard_service' => DashboardService::class,
    'sidebar_service' => SidebarService::class,
    'dashboard_view' => 'easyadmin::admin.dashboard',
    'enforce_validation' => false
];
