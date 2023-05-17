<?php

use App\Services\SidebarService;
use App\Http\Controllers\DashboardController;
use Ynotz\EasyAdmin\Services\DashboardService;

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
    'dashboard_controller' => DashboardController::class,
    'dashboard_method' => 'dashboard',
    'sidebar_service' => SidebarService::class,
    'dashboard_view' => 'easyadmin::admin.dashboard',
    'enforce_validation' => false
];
