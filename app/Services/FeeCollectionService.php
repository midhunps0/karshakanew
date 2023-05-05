<?php
namespace App\Services;

use App\Models\Religion;
use App\Helpers\AppHelper;
use App\Models\FeeCollection;
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
        if (isset($data['start'])) {
            $query->where('receipt_date', '>=', AppHelper::formatDateForSave($data['start']));
        }
        if (isset($data['end'])) {
            $query->where('receipt_date', '<=', AppHelper::formatDateForSave($data['end']));
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
