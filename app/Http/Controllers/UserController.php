<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class UserController extends SmartController
{
    use HasMVConnector;

    public function __construct(public UserService $connectorService, Request $request){
        parent::__construct($request);
        // $this->itemName = 'districts';
        $this->indexView = 'admin.index';
        // $this->createView = 'accesscontrol::roles.create';
        // $this->editView = 'accesscontrol::roles.edit';
    }
}
