<?php
namespace App\Services;

use App\Models\User;
use App\Models\TradeUnion;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Ynotz\EasyAdmin\Services\FormHelper;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;
use Illuminate\Auth\Access\AuthorizationException;
use Ynotz\EasyAdmin\Services\IndexTable;

class TradeUnionService implements ModelViewConnector {
    use IsModelViewConnector;

    protected $exportsEnabled = true;
    protected $selectionEnabled = false;

    private $indexTable;


    public function __construct()
    {
        $this->modelClass = TradeUnion::class;
        $this->indexTable = new IndexTable();
    }
    protected function getPageTitle(): string
    {
        return 'Trade Unions';
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
        $name = ucfirst(Str::lower($this->getModelShortName()));
        if (!$this->authoriseCreate()) {
            throw new AuthorizationException('The user is not authorised to view '.$name.'.');
        }
        return [
            'title' => 'Trade Unions',
            'form' => FormHelper::makeForm(
                title: 'Create Trade Union',
                id: 'form_tradeunions_create',
                action_route: 'tradeunions.store',
                success_redirect_route: 'tradeunions.index',
                items: $this->getCreateFormElements(),
                label_position: 'side'
            )
        ];
    }

    public function getEditPageData($id): array
    {
        return [
            'title' => 'Trade Unions',
            '_old' => ($this->modelClass)::find($id),
            'form' => FormHelper::makeEditForm(
                title: 'Edit Trade Union',
                id: 'form_tradeunions_create',
                action_route: 'tradeunions.update',
                action_route_params: ['id' => $id],
                success_redirect_route: 'tradeunions.index',
                items: $this->getEditFormElements(),
                label_position: 'side'
            )
        ];
    }

    public function getStoreValidationRules(): array
    {
        return [
            'name' => ['required', 'unique:trade_unions,name'],
            'enabled' => ['required', 'bool']
        ];
    }

    public function getUpdateValidationRules($id): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('trade_unions')->ignore($id),
            ]
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
            FormHelper::makeCheckbox(
                key: 'enabled',
                label: 'Is Enabled?',
                toggle: true,
                displayText: ['Yes', 'No']
            )
        ];
    }

    public function authoriseCreate()
    {
        $u = User::find(auth()->user()->id);
        return $u->hasRole('System Admin') || $u->hasPermissionTo('Trade Union: Create');
    }

    public function prepareForStoreValidation($data)
    {
        $data['enabled'] = $data['enabled'] == 'true';
        return $data;
    }

    public function prepareForUpdateValidation($data)
    {
        $data['enabled'] = $data['enabled'] == 'true';
        return $data;
    }

}

?>
