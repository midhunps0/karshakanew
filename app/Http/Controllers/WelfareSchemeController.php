<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\District;
use Illuminate\Http\Request;
use App\Services\WelfareSchemeService;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Illuminate\Auth\Access\AuthorizationException;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class WelfareSchemeController extends SmartController
{
    use HasMVConnector;

    public function __construct(public WelfareSchemeService $connectorService, Request $request){
        parent::__construct($request);
        // $this->itemName = 'districts';
        $this->indexView = 'admin.index';
        // $this->createView = 'accesscontrol::roles.create';
        // $this->editView = 'accesscontrol::roles.edit';
    }
}
