<?php
namespace App\Services;

use Ynotz\AccessControl\Models\Permission;
use Ynotz\EasyAdmin\Services\FormHelper;
use Ynotz\EasyAdmin\Services\IndexTable;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class PermissionService implements ModelViewConnector {
    use IsModelViewConnector;
    private $indexTable;

    public function __construct()
    {
        $this->modelClass = Permission::class;
        $this->indexTable = new IndexTable();
    }
    protected function getPageTitle(): string
    {
        return 'Permission';
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
            'title' => 'Permission',
            'form' => FormHelper::makeForm(
                title: 'Create Permission',
                id: 'form_permission_create',
                action_route: 'permissions.store',
                success_redirect_route: 'permissions.index',
                items: $this->getCreateFormElements(),
                label_position: 'side'
            )
        ];
    }

    public function getEditPageData($id): array
    {
        return [
            'title' => 'Permission',
            '_old' => ($this->modelClass)::find($id),
            'form' => FormHelper::makeEditForm(
                title: 'Edit Permission',
                id: 'form_permission_create',
                action_route: 'permissions.update',
                action_route_params: ['id' => $id],
                success_redirect_route: 'permissions.index',
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
            // FormHelper::makeSelect(
            //     key: 'permissions',
            //     label: 'Permissions',
            //     options: Permission::all(),
            //     options_type: 'collection',
            //     options_id_key: 'id',
            //     options_text_key: 'name',
            //     options_src: [PermissionService::class, 'suggestList'],
            //     properties: [
            //         'required' => true,
            //         'multiple' => false
            //     ],
            // ),
        ];
    }
}

?>
