<?php
namespace App\Services;

use App\Models\Taluk;
use App\Models\Village;
use App\Models\District;
use App\Events\BusinessActionEvent;
use App\Models\WelfareScheme;
use Ynotz\EasyAdmin\Services\FormHelper;
use Ynotz\EasyAdmin\Services\IndexTable;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class WelfareSchemeService implements ModelViewConnector {
    use IsModelViewConnector;
    private $indexTable;

    public function __construct()
    {
        $this->modelClass = WelfareScheme::class;
        $this->indexTable = new IndexTable();
        $this->selectionEnabled = false;
        $this->exportsEnabled = false;
        $this->showAddButton = false;
    }

    // protected function relations(): array
    // {
    //     return [];
    // }

    protected function getPageTitle(): string
    {
        return 'Welfare Schemes';
    }

    // private function getQuery()
    // {
    //     return $this->modelClass::query();
    // }

    protected function getIndexHeaders(): array
    {
        return $this->indexTable->addHeaderColumn(
            title: 'name',
            sort: ['key' => 'name']
        )->addHeaderColumn(
            title: 'code',
            sort: ['key' => 'code']
        )->addHeaderColumn(
            title: 'is_enabled',
        )->addHeaderColumn(
            title: 'Actions'
        )->getHeaderRow();
    }

    protected function getIndexColumns(): array
    {
        return $this->indexTable->addColumn(
            fields: ['name'],
        )->addColumn(
            fields: ['code'],
        )->addColumn(
            fields: ['is_enabled'],
            component: 'easyadmin::display.boolean-field'
        )->addActionColumn(
            editRoute: $this->getEditRoute(),
            editPermission: auth()->user()->hasPermissionTo('Welfare Scheme: Edit')
            // deleteRoute: $this->getDestroyRoute()
        )->getRow();
    }

    // public function getDownloadCols(): array
    // {
    //     return [
    //         'id',
    //         'name'
    //     ];
    // }

    // public function getCreatePageData(): array
    // {
    //     return [
    //         'title' => 'Create Village',
    //         'form' => FormHelper::makeForm(
    //             title: 'Create Village',
    //             id: 'form_villages_create',
    //             action_route: 'villages.store',
    //             success_redirect_route: 'villages.index',
    //             items: $this->getCreateFormElements(),
    //             label_position: 'side'
    //         )
    //     ];
    // }

    public function getEditPageData($id): array
    {
        return [
            'title' => 'Welfare Scheme',
            '_old' => ($this->modelClass)::find($id),
            'form' => FormHelper::makeEditForm(
                title: 'Edit Welfare Scheme',
                id: 'form_welfare_schemes_edit',
                action_route: 'welfareschemes.update',
                action_route_params: ['id' => $id],
                success_redirect_route: 'welfareschemes.index',
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
                properties: ['required' => true, 'readonly' => true],
                fireInputEvent: true,
            ),
            FormHelper::makeInput(
                inputType: 'text',
                key: 'code',
                label: 'Code',
                properties: ['required' => true, 'readonly' => true],
            ),
            FormHelper::makeCheckbox(
                key: 'is_enabled',
                label: 'Is enabled?',
                toggle: true,
                displayText: ['Yes', 'No']
            )
        ];
    }

    // public function getVillages($id)
    // {
    //     return Village::where('taluk_id', $id)->get()->pluck('name', 'id');
    // }

    // public function processBeforeStore(array $data): array
    // {
    //     $data['taluk_id'] = $data['taluk'];
    //     unset($data['taluk']);
    //     return $data;
    // }

    // public function processBeforeUpdte(array $data): array
    // {
    //     $data['taluk_id'] = $data['taluk'];
    //     unset($data['taluk']);
    //     return $data;
    // }

    public function getUpdateValidationRules(): array
    {
        return [
            'name' => ['string', 'required'],
            'code' => ['string', 'required'],
            'is_enabled' => ['boolean', 'required'],
        ];
    }

    public function prepareForUpdateValidation(array $data): array
    {
        $data['is_enabled'] = filter_var($data['is_enabled'], FILTER_VALIDATE_BOOLEAN);
        return $data;
    }

    public function processAfterStore($instance): void
    {
        BusinessActionEvent::dispatch(
            WelfareScheme::class,
            $instance->id,
            'Created',
            auth()->user()->id,
            null,
            $instance,
            'Created Welfare Scheme: '.$instance->name.', id: '.$instance->id,
        );
    }

    public function processAfterUpdate($oldInstance, $instance): void
    {
        BusinessActionEvent::dispatch(
            WelfareScheme::class,
            $instance->id,
            'Updated',
            auth()->user()->id,
            $oldInstance,
            $instance,
            'Updated Welfare Scheme: '.$instance->name.', id: '.$instance->id,
        );
    }
}

?>
