<?php
namespace App\Services;

use App\Models\FeeItem;
use App\Models\Religion;
use App\Helpers\AppHelper;
use App\Models\FeeCollection;
use Illuminate\Support\Carbon;
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
        info($data);
        $fc = FeeCollection::where('id', $id)->with(['feeItems', 'member'])->get()->first();
        $sum = 0;
        if ($fc != null) {
            $fc->receipt_date = AppHelper::formatDateForSave($data['date']);
            $fc->notes = $data['notes'];
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
            $fc->save();
            $fc->refresh();
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
            'feeItems', 'member', 'collectedBy', 'paymentMode'
        );
        $query->userDistrictConstrained();
        $datetype = $data['datetype'];
        if (isset($data['created_by'])) {
            $query->where('collected_by', $data['created_by']);
        }
        if (isset($data['start'])) {
            $query->where($datetype, '>=', AppHelper::formatDateForSave($data['start']));
        }
        if (isset($data['end'])) {
            $query->where($datetype, '<=', AppHelper::formatDateForSave($data['end']));
        }

        if (isset($data['fullreport']) && $data['fullreport']) {
            return $query->get();
        } else {
            return $query->paginate(
                perPage: 20,
                page: $data['page']
            );
        }
    }

    public function search($data)
    {
        $query = FeeCollection::with(
            'feeItems', 'member', 'collectedBy', 'paymentMode'
        );
        $query->userDistrictConstrained();
        if ($data['searchBy'] == 'receipt_no' && isset($data['receipt_no'])) {
            $query->where('receipt_number', $data['receipt_no']);
        } else {
            $datetype = $data['searchBy'];
            if (isset($data['start'])) {
                $query->where($datetype, '>=', AppHelper::formatDateForSave($data['start']));
            }
            if (isset($data['end'])) {
                $query->where($datetype, '<=', AppHelper::formatDateForSave($data['end']));
            }
        }

        if (isset($data['fullreport']) && $data['fullreport']) {
            return $query->get();
        } else {
            return $query->paginate(
                perPage: 20,
                page: $data['page']
            );
        }
    }
}

?>
