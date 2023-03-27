<?php

namespace App\Http\Controllers;

use App\Services\TradeUnionService;
use Illuminate\Http\Request;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class TradeUnionController extends SmartController
{
    use HasMVConnector;

    public function __construct(public TradeUnionService $connectorService, Request $request){
        parent::__construct($request);
    }
}
