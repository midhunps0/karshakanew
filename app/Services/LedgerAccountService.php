<?php

namespace App\Services;

use Illuminate\Support\Facades\Gate;
use Ynotz\EasyAdmin\Services\RowLayout;
use App\Models\Accounting\LedgerAccount;
use Ynotz\EasyAdmin\Services\FormHelper;
use Ynotz\EasyAdmin\Services\IndexTable;
use Ynotz\EasyAdmin\Services\ColumnLayout;
use Illuminate\Validation\UnauthorizedException;
use PhpOffice\PhpSpreadsheet\Worksheet\Column;
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
            'title' => 'Members',
            'form' => FormHelper::makeForm(
                title: 'Create Ledger Account',
                id: 'form_members_create',
                action_route: 'members.store',
                success_redirect_route: 'members.show',
                success_redirect_key: 'id',
                cancel_route: 'dashboard',
                items: $this->getCreateFormElements(),
                layout: $this->buildCreateFormLayout(),
                label_position: 'top',
                width: 'full',
                type: 'easyadmin::partials.simpleform',
            ),
            // '_old' => [
            //     'aadhaar_no' => $aadhaarNo
            // ]
        ];
    }

    public function getEditPageData(): array
    {}

    private function formElements(LedgerAccount $account = null): array
    {
        return [
            'name' => FormHelper::makeInput(
                inputType: 'text',
                key: 'name',
                label: 'Name',
                properties: ['required' => true],
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
                                (new ColumnLayout())
                                    ->addInputSlot('name')
                        ]
                    )
                ]
            );
        return $layout->getLayout();
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
