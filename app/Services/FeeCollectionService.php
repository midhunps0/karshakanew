<?php
namespace App\Services;

use App\Models\FeeItem;
use App\Models\Religion;
use App\Helpers\AppHelper;
use App\Models\FeeCollection;
use Illuminate\Support\Carbon;
use App\Events\BusinessActionEvent;
use App\Models\FeeType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\UnauthorizedException;
use Ynotz\EasyAdmin\Services\FormHelper;
use Ynotz\EasyAdmin\Services\IndexTable;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class FeeCollectionService implements ModelViewConnector {
    use IsModelViewConnector;

    private $indexTable;

    public function __construct()
    {
        $this->modelClass = FeeCollection::class;
        $this->indexTable = new IndexTable();
    }
    protected function getPageTitle(): string
    {
        return 'Receipt';
    }

    protected function getIndexHeaders(): array
    {
        $this->indexTable->addHeaderColumn(
            title: 'Name',
            sort: ['key' => 'name'],
        )->addHeaderColumn(
            title: 'Actions'
        )->getHeaderRow();
    }

    protected function getIndexColumns(): array
    {
        $this->indexTable->addColumn(
            fields: ['name'],
        )->addActionColumn(
            editRoute: $this->getEditRoute(),
            deleteRoute: $this->getDestroyRoute()
        )->getRow();
    }

    public function getDownloadCols(): array
    {
        return [
            'id',
            'name'
        ];
    }

    public function getCreatePageData(): array
    {
        return [
            // 'title' => 'Create Fee Collection',
            'form' => [
                'id' => 'form_fee_collections_create',
                // 'action_route' => 'fee_collections.store',
                // 'success_redirect_route' => 'fee_collections.index',
                // 'label_position' => 'side', //top/side/float
                'items' => [
                    // FormHelper::makeInput(
                    //     inputType: 'text',
                    //     key: 'name',
                    //     label: 'Name',
                    //     properties: ['required' => true],
                    //     fireInputEvent: true
                    // ),
                    // FormHelper::makeSelect(
                    //     key: 'taluks',
                    //     label: 'Taluks',
                    //     options: Religion::all(),
                    //     options_type: 'collection',
                    //     options_id_key: 'id',
                    //     options_text_key: 'name',
                    //     options_src: [ReligionService::class, 'suggestList'],
                    //     properties: [
                    //         'required' => true,
                    //         'multiple' => false
                    //     ],
                    // ),
                    // FormHelper::makeCheckbox(
                    //     key: 'verified',
                    //     label: 'Is verified?',
                    //     toggle: true,
                    //     displayText: ['Yes', 'No']
                    // )
                ]
            ]
        ];
    }

    public function getEditPageData($id): array
    {
        return [
            // 'title' => 'Create Fee Collection',
            'receipt' => FeeCollection::with(['member', 'feeItems'])->where('id', $id)->get()->first(),
            'form' => [
                'id' => 'form_fee_collections_edit',

                'items' => []
            ]
        ];
    }

    public function update($data, $id)
    {
        $fc = FeeCollection::where('id', $id)->with(['feeItems', 'member'])->get()->first();
        if (!($fc->is_editable_period || (auth()->user()->hasPermissionTo('Fee Collection: Edit In Own District Any Time') && $fc->district_id == auth()->user()->district_id))) {
            throw new UnauthorizedException('You are not allowed to update this resource.');
        }
        $oldFc = $fc;
        $sum = 0;
        if ($fc != null) {
            $fc->receipt_date = AppHelper::formatDateForSave($data['date']);
            $fc->notes = $data['notes'] ?? '';
            FeeItem::where('fee_collection_id', $id)->delete();
            foreach ($data['fee_item'] as $fi) {
                $fidata = [];
                $fidata['fee_collection_id'] = $id;
                $fidata['fee_type_id'] = $fi['fee_type_id'];
                if (isset($fi['period_from'])) {
                    $fidata['period_from'] = AppHelper::formatDateForSave($fi['period_from']);
                }
                if (isset($fi['period_to'])) {
                    $fidata['period_to'] = AppHelper::formatDateForSave($fi['period_to']);
                }
                if (isset($fi['period_to']) && isset($fi['period_to'])) {
                    $fidata['tenure'] = $fi['period_from']. ' to '. $fi['period_to'];
                }
                $fidata['amount'] = $fi['amount'];
                $sum += floatval($fi['amount']);
                FeeItem::create($fidata);
            }
            $fc->total_amount = $sum;
            $fc->save();
            $fc->refresh();
            BusinessActionEvent::dispatch(
                FeeCollection::class,
                $fc->id,
                'Created',
                auth()->user()->id,
                $oldFc,
                $fc,
                'Updated Receipt with id: '.$fc->id,
            );
            return [
                'success' => true,
                'receipt' => $fc
            ];
        } else {
            return false;
        }
    }
    public function fetch($id)
    {
        return FeeCollection::with(
            'feeItems', 'member', 'collectedBy', 'paymentMode'
        )->where('id', $id)->get()->first();
    }

    public function report($data)
    {
        $query = FeeCollection::with(
            'district', 'feeItems', 'member', 'collectedBy', 'paymentMode'
        );
        $query->userConstrained();
        $datetype = $data['datetype'];
        if (isset($data['created_by'])) {
            $query->where('collected_by', $data['created_by']);
        }
        if (isset($data['start'])) {
            $query->where($datetype, '>=', AppHelper::formatDateForSave(thedate: $data['start'], setTimeTo: 'start'));
        }
        if (isset($data['end'])) {
            $query->where($datetype, '<=', AppHelper::formatDateForSave(thedate: $data['end'], setTimeTo: 'end'));
        }
        $query->orderBy('fee_collections.id');
        if (isset($data['fullreport']) && $data['fullreport']) {
            return $query->get();
        } else {
            return $query->paginate(
                perPage: 100,
                page: $data['page']
            );
        }
    }

    public function aggregates($data)
    {
        // 'start'
        // 'end'
        // 'page'
        // 'datetype'
        // 'created_by'
        $datetype = $data['datetype'];
        $from = Carbon::createFromFormat('d-m-Y', $data['start'])->startOfDay()->format('Y-m-d');
        $to = Carbon::createFromFormat('d-m-Y', $data['end'])->addDay()->startOfDay()->format('Y-m-d');
        // dd($from, $to);
        $query = DB::table('fee_collections as fc')
            ->join('fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id')
            ->join('fee_types as ft', 'fi.fee_type_id', '=', 'ft.id')
            ->where('fc.receipt_date', '>=', $from)
            ->where('fc.receipt_date', '<', $to)
            ->whereNull('fc.deleted_at');

        if (isset($data['created_by'])) {
            $query->where('collected_by', $data['created_by']);
        }
        if (!auth()->user()->hasPermissionTo('Fee Collection: View In Any District')) {
            $query->where('fc.district_id', auth()->user()->district_id);
        } else if (isset($chosenDistrictId)) {
            $query->where('fc.district_id', $chosenDistrictId);
        }

        $aggregates = $query->select([
            DB::raw("ft.id as ftid"),
            DB::raw("ft.name as ftname"),
            DB::raw("COUNT(DISTINCT fc.id) as c_count"),
            DB::raw("SUM(fi.amount) as c_sum"),
        ])->groupBy('ft.id')->get();
        // dd($aggregates);
        // ->get();
        // return $aggregates;
        $feeTypes = FeeType::orderBy('name')->get()->pluck('name');

        $formatted = [];
        $total = 0;
        $tcount = 0;
        foreach ($feeTypes as $f) {
            foreach ($aggregates as $a) {
                if ($a->ftname == $f) {
                    $formatted[$f] = $a;
                    $total += $a->c_sum;
                    $tcount += $a->c_count;
                    break;
                }
            }
        }
        $totItem = new \stdClass();
        $totItem->ftid = null;
        $totItem->ftname = 'Total';
        $totItem->c_sum = $total;
        $totItem->c_count = $tcount;

        $formatted['Total'] = $totItem;
        return $formatted;

    }

    public function search($data)
    {
        $query = FeeCollection::with(
            'feeItems', 'member', 'collectedBy', 'paymentMode'
        );
        $query->userDistrictConstrained();
        if ($data['searchBy'] == 'receipt_no' && isset($data['receipt_no'])) {
            $rno = trim($data['receipt_no']).'%';
            $query->where('receipt_number', 'like', $rno);
        } else {
            $datetype = $data['searchBy'];
            if (isset($data['start'])) {
                $query->where($datetype, '>=', AppHelper::formatDateForSave($data['start']));
            }
            if (isset($data['end'])) {
                $query->where($datetype, '<=', AppHelper::formatDateForSave($data['end']));
            }
        }
        $query->orderBy('id', 'asc');
        if (isset($data['fullreport']) && $data['fullreport']) {
            return $query->get();
        } else {
            // $result = $query->paginate(
            //     perPage: 20,
            //     page: $data['page']
            // );
            // info('query results');
            // info($result);
            // return $result;
            return $query->paginate(
                perPage: 50,
                page: $data['page']
            );
        }
    }
    // public function processAfterStore($instance): void
    // {
    //     BusinessActionEvent::dispatch(
    //         FeeCollection::class,
    //         $instance->id,
    //         'Created',
    //         auth()->user()->id,
    //         null,
    //         $instance,
    //         'Created Receipt with id: '.$instance->id,
    //     );
    // }
}

?>
