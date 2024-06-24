<?php
namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Facades\DB;

class GovApiService
{
    public function membersData($pagination = true, $page = 1, $items_per_page = 100)
    {
        info('pagination');
        info($pagination);
        info('page');
        info($page);
        info('items_per_page');
        info($items_per_page);
        $query = DB::table('members as m')
            ->join('districts as d', 'd.id', '=', 'm.district_id')
            ->join('taluks as t', 't.id', '=', 'm.taluk_id')
            ->join('villages as v', 'v.id', '=', 'm.village_id')
            ->select(
                config('generalSettings.gov_member_data_list')
            )->whereNull('m.deleted_at');
        if (!$pagination) {
            return ['data' => $query->get()];
        }
        return $query->paginate(perPage: $items_per_page, page: $page);
    }
}
