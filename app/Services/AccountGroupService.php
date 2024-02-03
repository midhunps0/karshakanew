<?php

namespace App\Services;

use Illuminate\Support\Facades\Gate;
use App\Models\Accounting\AccountGroup;
use App\Models\Accounting\LedgerAccount;
use App\Models\District;
use Illuminate\Validation\UnauthorizedException;
use Ynotz\EasyAdmin\InputUpdateResponse;
use Ynotz\EasyAdmin\Services\ColumnLayout;
use Ynotz\EasyAdmin\Services\FormHelper;
use Ynotz\EasyAdmin\Services\IndexTable;
use Ynotz\EasyAdmin\Services\RowLayout;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;

class AccountGroupService
{
    use IsModelViewConnector;

    private $indexTable;

    public function __construct()
    {
        $this->modelClass = AccountGroup::class;
        $this->indexTable = new IndexTable();
        $this->searchesMap = [
            // 'taluk' => 'taluk_id',
            // 'district' => 'district_id',
            // 'village' => 'village_id'
        ];
        $this->selectionEnabled = false;
    }

    protected function relations(): array
    {
        return [
            // 'relation_name' => [
            //     'type' => '',
            //     'field' => '',
            //     'search_fn' => function ($query, $op, $val) {}, // function to be executed on search
            //     'search_scope' => '', //optional: required only for combined fields search
            //     'sort_scope' => '', //optional: required only for combined fields sort
            //     'models' => '' //optional: required only for morph types of relations
            // ],
            'parentGroup' => []
        ];
    }

    private function getQuery()
    {
        return AccountGroup::userDistrictConstrained();
    }

    public function showWith(): array
    {
        return [
            'parentGroup',
            // 'group'
        ];
    }

    protected function getPageTitle(): string
    {
        return 'Accounts Groups';
    }

    protected function getIndexHeaders(): array
    {
        return $this->indexTable->addHeaderColumn('name')
            ->addHeaderColumn(title: 'Parent Group')
            ->addHeaderColumn('Actions')
            ->getHeaderRow();
    }

    protected function getIndexColumns()
    {
        return $this->indexTable->addColumn(
            fields: ['name']
        )->addColumn(
            fields: ['name'],
            relation: 'parentGroup'
        )->addActionColumn(
            editRoute: 'accountgroups.edit',
            deleteRoute: 'accountgroups.destroy'
        )->getRow();
    }

    public function getCreatePageData(): array
    {
        return [
            'title' => '',
            'form' => FormHelper::makeForm(
                title: 'Create Account Group',
                id: 'form_members_create',
                action_route: 'accountgroups.store',
                success_redirect_route: 'accountgroups.index',
                success_redirect_key: 'id',
                cancel_route: 'dashboard',
                items: $this->getCreateFormElements(),
                layout: $this->buildCreateFormLayout(),
                label_position: 'top',
                width: '3/4',
                type: 'easyadmin::partials.simpleform',
            ),
            '_old' => [
                'district' => json_encode(['id' => auth()->user()->district_id])
            ]
        ];
    }

    public function getEditPageData($id): array
    {
        return [
            'title' => '',
            'form' => FormHelper::makeEditForm(
                title: 'Edit Account Group',
                id: 'form_members_edit',
                action_route: 'accountgroups.update',
                action_route_params: ['id' => $id],
                success_redirect_route: 'accountgroups.index',
                cancel_route: 'dashboard',
                items: $this->getCreateFormElements(),
                layout: $this->buildCreateFormLayout(),
                label_position: 'top',
                width: '3/4',
                type: 'easyadmin::partials.simpleform',
            ),
            '_old' => AccountGroup::with(['district'])->where('id', $id)->get()->first()
        ];
    }

    private function formElements(): array
    {
        $districtPermission = auth()->user()->hasPermissionTo('Accounts Group: View In Any District');
        // dd($districtPermission);
        return [
            'district' => FormHelper::makeSelect(
                key: 'district',
                label: 'District',
                options: District::all(),
                properties: ['required' => true,],
                fireInputEvent: true,
                show: $districtPermission
            ),
            'name' => FormHelper::makeInput(
                inputType: 'text',
                key: 'name',
                label: 'Name',
                properties: ['required' => true],
            ),
            'parentGroup' => FormHelper::makeSelect(
                key: 'parentGroup',
                label: 'Parent Group',
                options: AccountGroup::userDistrictConstrained()->get(),
                properties: ['required' => true],
                updateOnEvents: ['district'],
                options_src: [$this::class, 'groupsForDistrict']
            ),
        ];
    }

    public function buildCreateFormLayout(): array
    {
        $layout = (new ColumnLayout())
            ->addElements(
                [
                    (new RowLayout())->addElements(
                        [
                                (new ColumnLayout($width="1/2"))
                                    ->addInputSlot('district'),
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                                (new ColumnLayout($width="1/2"))
                                    ->addInputSlot('parentGroup'),
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                                (new ColumnLayout($width="1/2"))
                                    ->addInputSlot('name'),
                        ]
                    ),
                ]
            );
        return $layout->getLayout();
    }

    public function groupsForDistrict($request)
    {
        $groups = AccountGroup::userDistrictConstrained($request['depended_id'])->get();
        return new InputUpdateResponse(
            $groups,
            'success',
            true
        );
    }

    public function prepareForStoreValidation(array $data): array
    {
        $data['district_id'] = $data['district'];
        unset($data['district']);
        $data['parent_id'] = $data['parentGroup'];
        unset($data['parentGroup']);
        return $data;
    }

    public function prepareForUpdateValidation(array $data): array
    {
        $data['district_id'] = $data['district'];
        unset($data['district']);
        $data['parent_id'] = $data['parentGroup'];
        unset($data['parentGroup']);
        return $data;
    }

    public function getStoreValidationRules(): array
    {
        $rules =  [
            'district_id' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'parent_id' => ['required', 'integer'],
        ];

        return $rules;
    }

    public function authoriseStore()
    {
        $user = auth()->user();
        if ($user->hasPermissionTo('Accounts Group: Create In Any District')
            || $user->hasPermissionTo('Accounts Group: Create In Own District')) {
            return true;
        }
        return false;
    }

    public function authoriseUpdate($account)
    {
        $user = auth()->user();
        if ($user->hasPermissionTo('Accounts Group: Edit In Any District')
            || ($user->hasPermissionTo('Accounts Group: Edit In Own District') && $user->district_id == $account->district_id)) {
            return true;
        }
        return false;
    }

    public function accountsChart($districtId = null)
    {
        return AccountGroup::with(['subGroupsFamilyAccounts'])->where('parent_id', null)
            ->userDistrictConstrained($districtId)
            ->get();
    }
}
