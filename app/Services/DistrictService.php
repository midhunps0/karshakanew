<?php
namespace App\Services;

use App\Models\Taluk;
use App\Models\District;
use App\Events\BusinessActionEvent;
use Ynotz\EasyAdmin\Services\FormHelper;
use Ynotz\EasyAdmin\Services\IndexTable;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class DistrictService implements ModelViewConnector {
    use IsModelViewConnector;
    private $indexTable;

    public function __construct()
    {
        $this->modelClass = District::class;
        $this->indexTable = new IndexTable();
    }
    protected function getPageTitle(): string
    {
        return 'Districts';
    }

    protected function getIndexHeaders(): array
    {
        return $this->indexTable->addHeaderColumn(
            title: 'name',
            sort: ['key' => 'name']
        )->addHeaderColumn(
            title: 'Actions'
        )->getHeaderRow();
    }

    protected function getIndexColumns(): array
    {
        return $this->indexTable->addColumn(
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
            'title' => 'Districts',
            'form' => FormHelper::makeForm(
                title: 'Create District',
                id: 'form_districts_create',
                action_route: 'districts.store',
                success_redirect_route: 'districts.index',
                items: $this->getCreateFormElements(),
                label_position: 'side'
            )
        ];
    }

    public function getEditPageData($id): array
    {
        return [
            'title' => 'Districts',
            '_old' => ($this->modelClass)::find($id),
            'form' => FormHelper::makeEditForm(
                title: 'Edit District',
                id: 'form_districts_create',
                action_route: 'districts.update',
                action_route_params: ['id' => $id],
                success_redirect_route: 'districts.index',
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
                key: 'short_code',
                label: 'Short Code',
                properties: ['required' => true, 'maxlength' => 3],
            ),
            // FormHelper::makeSelect(
            //     key: 'taluks',
            //     label: 'Taluks',
            //     options: Taluk::all(),
            //     options_type: 'collection',
            //     options_id_key: 'id',
            //     options_text_key: 'name',
            //     options_src: [RoleService::class, 'suggestList'],
            //     properties: [
            //         'required' => true,
            //         'multiple' => false
            //     ],
            // ),
            FormHelper::makeCheckbox(
                key: 'verified',
                label: 'Is verified?',
                toggle: true,
                displayText: ['Yes', 'No']
            )
        ];
    }

    public function getTaluks($id)
    {
        return Taluk::where('district_id', $id)->get()->pluck('name', 'id');
    }

    public function processAfterStore($instance): void
    {
        BusinessActionEvent::dispatch(
            District::class,
            $instance->id,
            'Created',
            auth()->user()->id,
            null,
            $instance,
            'Created District with id: '.$instance->id,
        );
    }

    public function processAfterUpdate($oldInstance, $instance): void
    {
        BusinessActionEvent::dispatch(
            District::class,
            $instance->id,
            'Updated',
            auth()->user()->id,
            $oldInstance,
            $instance,
            'Updated District with id: '.$instance->id,
        );
    }
}

?>
