<?php
namespace App\Services;

use App\Models\User;
use App\Models\District;
use App\Services\RoleService;
use App\Events\BusinessActionEvent;
use Illuminate\Support\Facades\Hash;
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
        $this->selectionEnabled = false;
    }

    protected function relations()
    {
        return [
            'roles' => [
                'search_column' => 'id',
                'filter_column' => 'id',
                'sort_column' => 'id',
            ],
            'district' => [
                'search_column' => 'id',
                'filter_column' => 'id',
                'sort_column' => 'id',
            ],
        ];
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
            filter: ['key' => 'roles', 'options' => Role::all()->pluck('name', 'id')]
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
        return [];
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
                properties: ['required' => true]
            ),
            FormHelper::makeInput(
                inputType: 'text',
                key: 'username',
                label: 'Username',
                properties: ['required' => true]
            ),
            FormHelper::makeInput(
                inputType: 'text',
                key: 'email',
                label: 'Email',
                properties: ['required' => true,]
            ),
            FormHelper::makeInput(
                inputType: 'text',
                key: 'password',
                label: 'Password',
                properties: ['required' => true,],
                formTypes: ['create']
            ),
            FormHelper::makeSelect(
                key: 'district',
                label: 'District',
                options: District::all()->pluck('name', 'id'),
                options_type: 'key_value',
                properties: ['required' => true],
            ),
            FormHelper::makeSelect(
                key: 'roles',
                label: 'Role',
                options: Role::all(),
                options_type: 'collection',
                options_id_key: 'id',
                options_text_key: 'name',
                options_src: [RoleService::class, 'suggestList'],
                properties: [
                    'required' => true,
                    'multiple' => true
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
        ])->userAccessControlled()
        ->exceptSelf();
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

    public function getStoreValidationRules(): array
    {
        return [
            'name' => ['required', 'string'],
            'username' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'district' => ['required', 'integer'],
            'roles.*' => ['required'],

        ];
    }

    public function getUpdateValidationRules($id): array
    {
        $arr = $this->getStoreValidationRules();
        unset($arr['password']);
        return $arr;
    }

    public function processBeforeStore(array $data): array
    {
        $data['password'] = Hash::make($data['password']);

        return $data;
    }

    public function processAfterStore($instance): void
    {
        BusinessActionEvent::dispatch(
            User::class,
            $instance->id,
            'Created',
            auth()->user()->id,
            null,
            $instance,
            'Created User: '.$instance->name.', id: '.$instance->id,
        );
    }

    public function processAfterUpdate($oldInstance, $instance): void
    {
        BusinessActionEvent::dispatch(
            User::class,
            $instance->id,
            'Updated',
            auth()->user()->id,
            $oldInstance,
            $instance,
            'Updated User: '.$instance->name.', id: '.$instance->id,
        );
    }
}

?>
