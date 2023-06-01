<?php
namespace App\Services;

use Ynotz\AccessControl\Models\Role;
use Ynotz\EasyAdmin\Services\FormHelper;
use Ynotz\EasyAdmin\Services\IndexTable;
use Ynotz\AccessControl\Models\Permission;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;
use Ynotz\AccessControl\Services\PermissionService;

class RoleService implements ModelViewConnector {
    use IsModelViewConnector;
    private $indexTable;

    public function __construct()
    {
        $this->modelClass = Role::class;
        $this->indexTable = new IndexTable();
    }

    public function getIndexdata()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return [
            'roles' => $roles,
            'permissions' => $permissions
        ];
    }

    protected function getPageTitle(): string
    {
        return 'Role';
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
            'title' => 'Role',
            'form' => FormHelper::makeForm(
                title: 'Create Role',
                id: 'form_role_create',
                action_route: 'roles.store',
                success_redirect_route: 'roles.index',
                items: $this->getCreateFormElements(),
                label_position: 'side'
            )
        ];
    }

    public function getEditPageData($id): array
    {
        return [
            'title' => 'Role',
            '_old' => ($this->modelClass)::find($id),
            'form' => FormHelper::makeEditForm(
                title: 'Edit Role',
                id: 'form_role_create',
                action_route: 'roles.update',
                action_route_params: ['id' => $id],
                success_redirect_route: 'roles.index',
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
            FormHelper::makeSelect(
                key: 'permissions',
                label: 'Permissions',
                options: Permission::all(),
                options_type: 'collection',
                options_id_key: 'id',
                options_text_key: 'name',
                options_src: [PermissionService::class, 'suggestList'],
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

    public function permissionUpdate($data)
    {
        try {
            /**
             * @var Role
             */
            $role = Role::with('permissions')->where('id', $data['role_id'])->get()->first();
            $existing_permissions = $role->permissions()->pluck('id')->toArray();
            switch($data['granted']) {
                case 1:
                    if (!in_array($data['permission_id'], $existing_permissions)) {
                        $role->assignPermissions($data['permission_id']);
                    }
                    break;
                case 0:
                    if (in_array($data['permission_id'], $existing_permissions)) {
                        $role->reomvePermissions($data['permission_id']);
                    }
                    break;
            }
            return true;
        } catch (\Throwable $e) {
            info($e->__toString());
            return false;
        }
    }
}

?>
