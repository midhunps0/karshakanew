<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\Request;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class RoleController extends SmartController
{
    use HasMVConnector;

    public function __construct(public RoleService $connectorService, Request $request){
        parent::__construct($request);
        // $this->itemName = 'districts';
        $this->indexView = 'admin.roles.index';
        // $this->createView = 'accesscontrol::roles.create';
        // $this->editView = 'accesscontrol::roles.edit';
    }

    public function index()
    {
        return $this->buildResponse($this->indexView, $this->connectorService->getIndexData());
    }
}
