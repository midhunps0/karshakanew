<?php
namespace App\Services;

use App\Models\Caste;
use App\Models\Religion;
use Ynotz\EasyAdmin\Services\FormHelper;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;
use Ynotz\EasyAdmin\Services\IndexTable;

class ReligionService implements ModelViewConnector {
    use IsModelViewConnector;

    private $indexTable;

    public function __construct()
    {
        $this->modelClass = Religion::class;
        $this->indexTable = new IndexTable();
    }
    protected function getPageTitle(): string
    {
        return 'Districts';
    }

    protected function getIndexHeaders(): array
    {
        $this->indexTable->addHeaderColumn(
            title:'Name',
            sort: ['key' => 'name']
        )->addHeaderColumn(
            title: 'Actions'
        )->getHeaderRow();
    }

    protected function getIndexColumns(): array
    {
        return $this->indexTable->addColumn(
            fields: ['name']
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
            'title' => 'Create Religion',
            'form' => [
                'id' => 'form_religions_create',
                'action_route' => 'religions.store',
                'success_redirect_route' => 'religions.index',
                'label_position' => 'side', //top/side/float
                'items' => [
                    FormHelper::makeInput(
                        inputType: 'text',
                        key: 'name',
                        label: 'Name',
                        properties: ['required' => true],
                        fireInputEvent: true
                    ),
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
}

?>
