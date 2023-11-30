<?php

namespace App\Services;

use App\Models\District;
use Illuminate\Support\Facades\Gate;
use App\Models\Accounting\AccountGroup;
use Ynotz\EasyAdmin\Services\RowLayout;
use App\Models\Accounting\LedgerAccount;
use Ynotz\EasyAdmin\InputUpdateResponse;
use Ynotz\EasyAdmin\Services\FormHelper;
use Ynotz\EasyAdmin\Services\IndexTable;
use Ynotz\EasyAdmin\Services\ColumnLayout;
use PhpOffice\PhpSpreadsheet\Worksheet\Column;
use Illuminate\Validation\UnauthorizedException;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class LedgerAccountService implements ModelViewConnector
{
    use IsModelViewConnector;

    private $indexTable;

    public function __construct()
    {
        $this->modelClass = LedgerAccount::class;
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
            'district' => [
                'search_column' => 'id',
                'filter_column' => 'id',
                'sort_column' => 'id',
            ],
            'group' => [
                'search_column' => 'id',
                'filter_column' => 'id',
                'sort_column' => 'id',
            ],
        ];
    }

    public function showWith(): array
    {
        return [
            'district',
            'group'
        ];
    }

    protected function getPageTitle(): string
    {
        return 'Ledger Accounts';
    }

    protected function getIndexHeaders(): array
    {}

    protected function getIndexColumns(): array
    {}

    public function getCreatePageData(): array
    {
        return [
            'title' => '',
            'form' => FormHelper::makeForm(
                title: 'Create Ledger Account',
                id: 'form_members_create',
                action_route: 'ledgeraccounts.store',
                success_redirect_route: 'ledgeraccounts.show',
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
                title: 'Edit Ledger Account',
                id: 'form_members_edit',
                action_route: 'ledgeraccounts.update',
                action_route_params: ['id' => $id],
                success_redirect_route: 'ledgeraccounts.show',
                cancel_route: 'dashboard',
                items: $this->getCreateFormElements(),
                layout: $this->buildCreateFormLayout(),
                label_position: 'top',
                width: '3/4',
                type: 'easyadmin::partials.simpleform',
            ),
            '_old' => LedgerAccount::with(['district'])->where('id', $id)->get()->first()
        ];
    }

    private function formElements(LedgerAccount $account = null): array
    {
        $districtPermission = auth()->user()->hasPermissionTo('Ledger Account: View In Any District');
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
            'description' => FormHelper::makeTextarea(
                key: 'description',
                label: 'Description',
                properties: ['required' => false],
            ),
            'group' => FormHelper::makeSelect(
                key: 'group',
                label: 'Group',
                options: AccountGroup::where('parent_id', '<>', null)->UserDistrictConstrained()->get(),
                properties: ['required' => true],
                updateOnEvents: ['district'],
                options_src: [$this::class, 'groupsForDistrict']
            ),
            'opening_balance' => FormHelper::makeInput(
                inputType: 'text',
                key: 'opening_balance',
                label: 'Opening Balance',
                properties: ['required' => false],
            ),
            'opening_bal_type' => FormHelper::makeSelect(
                key: 'opening_bal_type',
                label: 'Opening Balance Type',
                options: ['credit' => 'Credit', 'debit' => 'Debit'],
                options_type: 'key_value',
                properties: ['required' => false],
            ),
            'cashorbank' => FormHelper::makeCheckbox(
                key: 'cashorbank',
                label: 'Is Cash/Bank Account?',
            )
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
                                    ->addInputSlot('group'),
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                                (new ColumnLayout($width="1/2"))
                                    ->addInputSlot('name'),
                                (new ColumnLayout($width="1/2"))
                                    ->addInputSlot('description')
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                                (new ColumnLayout($width="1/2"))
                                    ->addInputSlot('opening_balance'),
                                (new ColumnLayout($width="1/2"))
                                    ->addInputSlot('opening_bal_type'),
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                                (new ColumnLayout($width="1/2", $style="margin-top: 2rem;"))
                                    ->addInputSlot('cashorbank'),
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
        $data['group_id'] = $data['group'];
        unset($data['group']);
        $data['cashorbank'] = filter_var($data['cashorbank'], FILTER_VALIDATE_BOOLEAN);
        return $data;
    }

    public function prepareForUpdateValidation(array $data): array
    {
        $data['district_id'] = $data['district'];
        unset($data['district']);
        $data['group_id'] = $data['group'];
        unset($data['group']);
        $data['cashorbank'] = filter_var($data['cashorbank'], FILTER_VALIDATE_BOOLEAN);
        return $data;
    }

    public function getStoreValidationRules(): array
    {
        $rules =  [
            'district_id' => ['required', 'integer'],
            'group_id' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'description' => ['sometimes', 'nullable'],
            'opening_balance' => ['sometimes', 'nullable'],
            'opening_bal_type' => ['sometimes', 'nullable'],
            'cashorbank' => ['sometimes', 'boolean'],
        ];

        if(!empty($this->description) ) {
            $rules['description'] = 'string';
        }

        if(!empty($this->opening_balance) ) {
            $rules['opening_balance'] = 'numeric';
        }

        if(!empty($this->opening_bal_type) ) {
            $rules['opening_bal_type'] = 'string';
        }

        return $rules;
    }

    public function authoriseStore()
    {
        $user = auth()->user();
        if ($user->hasPermissionTo('Ledger Account: Create In Any District')
            || $user->hasPermissionTo('Ledger Account: Create In Own District')) {
            return true;
        }
        return false;
    }

    public function authoriseUpdate($account)
    {
        $user = auth()->user();
        if ($user->hasPermissionTo('Ledger Account: Edit In Any District')
            || ($user->hasPermissionTo('Ledger Account: Edit In Own District') && $user->district_id == $account->district_id)) {
            return true;
        }
        return false;
    }
/*
    public function model()
    {
        return LedgerAccount::class;
    }

    public function index($search, $cashorbank = null)
    {
        $query = LedgerAccount::query();
        if (isset($search)) {
            $query->where('name', 'like', '%'.$search.'%');
        }
        if (isset($cashorbank)) {
            $query->where('cashorbank', $cashorbank);
        }
        return $query->get();
    }

    public function findOrFail($id)
    {
        $account =  LedgerAccount::findOrFail($id);
        if (Gate::denies('view', [$account])) {
            return response()->json(['error' => 'Unauthorized action.'], 404);
        }
        return $account;
    }


    public function create($inputs)
    {
        $account = LedgerAccount::create($inputs);

        return $account;
    }

    public function update($inputs, $id, $attribute = 'id')
    {
        $account = LedgerAccount::find($id);
        if (Gate::denies('update', [$account])) {
            //return response()->json(['error' => 'Unauthorized action.'], 404);
            throw new UnauthorizedException('Unauthorized action', 404);
        }
        if (isset($inputs['name'])) {
            $account->name = $inputs['name'];
        }
        if (isset($inputs['description'])) {
            $account->description = $inputs['description'];
        }
        if (isset($inputs['group_id'])) {
            $account->group_id = $inputs['group_id'];
        }
        if (isset($inputs['opening_balance'])) {
            $account->opening_balance = $inputs['opening_balance'];
        }
        if (isset($inputs['opening_bal_type'])) {
            $account->opening_bal_type = $inputs['opening_bal_type'];
        }
        $account->save();
        $account->refresh();
        return $account;
    }
    */
}
