<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\District;
use Illuminate\Http\Request;
use App\Services\DistrictService;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Illuminate\Auth\Access\AuthorizationException;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class DistrictController extends SmartController
{
    use HasMVConnector;

    public function __construct(public DistrictService $connectorService, Request $request){
        parent::__construct($request);
        // $this->itemName = 'districts';
        $this->indexView = 'admin.index';
        // $this->createView = 'accesscontrol::roles.create';
        // $this->editView = 'accesscontrol::roles.edit';
    }

    public function getTaluks($id)
    {
        try {
            $result = $this->connectorService->getTaluks($id);
            return response()->json(
                [
                    'taluks' => $result
                ]
            );
        } catch (AuthorizationException $e) {
            info($e);
            return $this->buildResponse($this->unauthorisedView);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }
}
