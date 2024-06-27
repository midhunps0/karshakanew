<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GovApiMembersDataRequest;
use App\Services\GovApiService;
use Illuminate\Http\Request;

class GovApiController extends Controller
{
    public function membersData(Request $request)
    {
        $ipAddress = trim($request->ip());
        $govToken = trim($request->header('GOV-TOKEN'));
        if ($govToken == config('generalSettings.gov_token') && (config('generalSettings.gov_ip_address') == null || $ipAddress == config('generalSettings.gov_ip_address'))) {
            return (new GovApiService)->membersData(
                $request->input('pagination', true),
                $request->input('page', 1),
                $request->input('items_per_page', 100),
            );
        }

        return response()->json([
            'status' => '401',
            'message' => 'The Action Is Unauthorised.'
        ]);

    }
}
