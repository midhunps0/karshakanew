<?php
namespace App\Services;

use App\Models\User;
use App\Services\RoleService;
use Ynotz\AccessControl\Models\Role;
use Ynotz\EasyAdmin\Services\FormHelper;
use Ynotz\EasyAdmin\Services\IndexTable;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class UserService implements ModelViewConnector {
    use IsModelViewConnector;
    private $indexTable;

    public function __construct()
    {
        $this->modelClass = User::class;
        $this->indexTable = new IndexTable();
    }
    protected function getPageTitle(): string
    {
        return 'Users';
    }

    protected function getIndexHeaders(): array
    {
        return $this->indexTable->addHeaderColumn(
            title: 'name',
            sort: ['key' => 'name']
        )->addHeaderColumn(
            title: 'role',
            sort: ['key' => 'roles']
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
            relation: 'roles',
        )->addActionColumn(
            editRoute: $this->getEditRoute(),
            deleteRoute: $this->getDestroyRoute()
        )->getRow();
    }

    public function getAdvanceSearchFields(): array
    {
        return $this->indexTable->addSearchField(
            key: 'name',
            displayText: 'Name',
            valueType: 'string',
        )->getAdvSearchFields();
        return [
            'name' => [
                'key' => 'name',
                'text' => 'Name',
                'type' => 'string', // numeric|string|list_numeric|list_string
                'inputType' => 'text', // text|select
                // 'options' => [],
                // 'optionsType' => ''  // 'key_value' or 'value_only'
            ]
        ];
    }
    public function getDownloadCols(): array
    {
        return [
            'id',
            'name',
            'roles.name'
        ];
    }

    public function getDownloadColTitles(): array
    {
        return [
            'roles.name' => 'Role'
        ];
    }

    public function getCreatePageData(): array
    {
        return [
            'title' => 'Users',
            'form' => FormHelper::makeForm(
                title: 'Create User',
                id: 'form_users_create',
                action_route: 'users.store',
                success_redirect_route: 'users.index',
                items: $this->getCreateFormElements(),
                label_position: 'side'
            )
        ];
    }

    public function getEditPageData($id): array
    {
        return [
            'title' => 'Users',
            '_old' => ($this->modelClass)::find($id),
            'form' => FormHelper::makeEditForm(
                title: 'Edit User',
                id: 'form_users_create',
                action_route: 'users.update',
                action_route_params: ['id' => $id],
                success_redirect_route: 'users.index',
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
                key: 'role',
                label: 'Role',
                options: Role::all(),
                options_type: 'collection',
                options_id_key: 'id',
                options_text_key: 'name',
                options_src: [RoleService::class, 'suggestList'],
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

    private function getQuery()
    {
        return $this->modelClass::query()->with([
            'roles' => function ($query) {
                $query->select('id', 'name');
            }
        ]);
    }

    private function getShowAddButton(): bool
    {
        /**
         * @var User
        */
        $user = User::find(auth()->user()->id);
        return $user->hasAnyPermission(
            ['User: Create In Any District', 'User: Create In Own District']
        );
    }
}

?>
