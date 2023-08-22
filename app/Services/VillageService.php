<?php
namespace App\Services;

use App\Models\Taluk;
use App\Models\Village;
use App\Models\District;
use App\Events\BusinessActionEvent;
use Ynotz\EasyAdmin\Services\FormHelper;
use Ynotz\EasyAdmin\Services\IndexTable;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class VillageService implements ModelViewConnector {
    use IsModelViewConnector;
    private $indexTable;

    public function __construct()
    {
        $this->modelClass = Village::class;
        $this->indexTable = new IndexTable();
        $this->selectionEnabled = false;
        $this->exportsEnabled = false;
    }

    protected function relations(): array
    {
        return [
            'taluk' => [
                'search_column' => 'id',
                'filter_column' => 'id',
                'sort_column' => 'id',
            ],
        ];
    }

    protected function getPageTitle(): string
    {
        return 'Villages';
    }

    private function getQuery()
    {
        return $this->modelClass::query()
            ->userAccessControlled()
            ->with(
                [
                    'taluk' => function ($query) {
                        $query->select('id', 'name');
                    },

                ]
            );
    }

    protected function getIndexHeaders(): array
    {
        return $this->indexTable->addHeaderColumn(
            title: 'name',
            sort: ['key' => 'name']
        )->addHeaderColumn(
            title: 'taluk',
            sort: ['key' => 'taluk_id']
        )->addHeaderColumn(
            title: 'district',
            // sort: ['key' => 'district_id']
        )->addHeaderColumn(
            title: 'Actions'
        )->getHeaderRow();
    }

    protected function getIndexColumns(): array
    {
        return $this->indexTable->addColumn(
            fields: ['name'],
        )->addColumn(
            fields: ['name'],
            relation: 'taluk'
        )->addColumn(
            fields: ['district'],
            // relation: 'district'
        )->addActionColumn(
            editRoute: $this->getEditRoute(),
            // deleteRoute: $this->getDestroyRoute()
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
            'title' => 'Create Village',
            'form' => FormHelper::makeForm(
                title: 'Create Village',
                id: 'form_villages_create',
                action_route: 'villages.store',
                success_redirect_route: 'villages.index',
                items: $this->getCreateFormElements(),
                label_position: 'side'
            )
        ];
    }

    public function getEditPageData($id): array
    {
        return [
            'title' => 'Villages',
            '_old' => ($this->modelClass)::find($id),
            'form' => FormHelper::makeEditForm(
                title: 'Edit Village',
                id: 'form_villages_create',
                action_route: 'villages.update',
                action_route_params: ['id' => $id],
                success_redirect_route: 'villages.index',
                items: $this->getEditFormElements(),
                label_position: 'side'
            )
        ];
    }

    private function formElements(): array
    {
        return [
            FormHelper::makeInput(
                inputType: 'text',
                key: 'name',
                label: 'Name',
                properties: ['required' => true],
                fireInputEvent: true
            ),
            FormHelper::makeInput(
                inputType: 'text',
                key: 'display_code',
                label: 'Display Code',
                properties: ['required' => true,],
            ),
            FormHelper::makeSelect(
                key: 'taluk',
                label: 'Taluk',
                options: Taluk::userAccessControlled()->get(),
                options_type: 'collection',
                options_id_key: 'id',
                options_text_key: 'name',
                // options_src: [RoleService::class, 'suggestList'],
                properties: [
                    'required' => true,
                    'multiple' => false
                ],
            ),
            // FormHelper::makeCheckbox(
            //     key: 'verified',
            //     label: 'Is verified?',
            //     toggle: true,
            //     displayText: ['Yes', 'No']
            // )
        ];
    }

    public function getVillages($id)
    {
        return Village::where('taluk_id', $id)->get()->pluck('name', 'id');
    }

    public function processBeforeStore(array $data): array
    {
        $data['taluk_id'] = $data['taluk'];
        unset($data['taluk']);
        return $data;
    }

    public function processBeforeUpdte(array $data): array
    {
        $data['taluk_id'] = $data['taluk'];
        unset($data['taluk']);
        return $data;
    }

    public function processAfterStore($instance): void
    {
        BusinessActionEvent::dispatch(
            Taluk::class,
            $instance->id,
            'Created',
            auth()->user()->id,
            null,
            $instance,
            'Created Village: '.$instance->name.', id: '.$instance->id,
        );
    }

    public function processAfterUpdate($oldInstance, $instance): void
    {
        BusinessActionEvent::dispatch(
            Taluk::class,
            $instance->id,
            'Updated',
            auth()->user()->id,
            $oldInstance,
            $instance,
            'Updated Village: '.$instance->name.', id: '.$instance->id,
        );
    }
}

?>
