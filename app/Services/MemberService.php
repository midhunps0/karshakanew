<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Taluk;
use App\Models\Member;
use App\Models\FeeItem;
use App\Models\Village;
use App\Models\District;
use App\Helpers\AppHelper;
use App\Models\TradeUnion;
use Illuminate\Support\Str;
use App\Models\FeeCollection;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Events\BusinessActionEvent;
use App\Events\FeeCollectionEvent;
use Exception;
use Hamcrest\Type\IsNumeric;
use Ynotz\EasyAdmin\Services\RowLayout;
use Ynotz\EasyAdmin\Services\TabLayout;
use Ynotz\EasyAdmin\Services\TabsPanel;
use Ynotz\EasyAdmin\InputUpdateResponse;
use Ynotz\EasyAdmin\Services\FormHelper;
use Ynotz\EasyAdmin\Services\IndexTable;
use Ynotz\EasyAdmin\Services\ColumnLayout;
use Ynotz\EasyAdmin\Services\LayoutElement;
use Ynotz\EasyAdmin\Services\SectionDivider;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Ynotz\MediaManager\Services\EAInputMediaValidator;

use function PHPUnit\Framework\isNan;

class MemberService implements ModelViewConnector {
    use IsModelViewConnector;

    private $indexTable;
    private $request;

    protected $mediaFields = [
        'photo',
        'application_front',
        'application_back',
        'aadhaar_card',
        'bank_passbook',
        'ration_card',
        'wb_passbook_front',
        'wb_passbook_back',
        'one_and_same_cert',
        'other_doc'
    ];


    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->modelClass = '\\App\\Models\\Member';
        $this->indexTable = new IndexTable();
        $this->searchesMap = [
            'taluk' => 'taluk_id',
            'district' => 'district_id',
            'village' => 'village_id'
        ];
        $this->selectionEnabled = false;
        $this->exportsEnabled = false;
    }
    protected function getPageTitle(): string
    {
        return 'Members';
    }

    protected function getIndexHeaders(): array
    {
        $columns = $this->indexTable->addHeaderColumn(
            title: 'Name',
            // search: [
            //     'key' => 'name',
            //     'condition' => 'ct',
            //     'label' => 'Search Members'
            // ],
            // sort: ['key' => 'name'],
        )->addHeaderColumn(
            title: 'Membership No.',
            // search: [
            //     'key' => 'membership_no',
            //     'condition' => 'st',
            //     'label' => 'Search Members'
            // ],
            // sort: ['key' => 'membership_no']
        )->addHeaderColumn(
            title: 'Address',
            // search: [
            //     'key' => 'aadhaar_no',
            //     'condition' => 'st',
            //     'label' => 'Search Members'
            // ],
        );

        $user = User::find(auth()->user()->id);

        if ($user->hasPermissionTo('Member: View In Any District')) {
            $columns = $columns->addHeaderColumn(
                title: 'District',
                // filter: [
                //     'key' => 'district_id',
                //     'options' => District::all()->pluck('name', 'id')
                // ],
            );
        }
        if ($user->hasPermissionTo('Member: View In Any District')) {
            $taluks = Taluk::all()->pluck('name', 'id');
        } else {
            $did = $user->district->id;
            $taluks = Taluk::inDistrict($did)->get()->pluck('name', 'id');
        }
        $columns = $columns->addHeaderColumn(
            title: 'Taluk',
            // filter: [
            //     'key' => 'taluk_id',
            //     'options' => Taluk::all()->pluck('name', 'id')
            // ],
        )->addHeaderColumn(
            title: 'Village'
        )->addHeaderColumn(
            title: 'Actions'
        );
        return $columns->getHeaderRow();
    }

    protected function getIndexColumns(): array
    {
        $columns = $this->indexTable->addColumn(
            fields: ['name', 'name_mal']
        )->addColumn(
            fields: ['membership_no']
        )->addColumn(
            fields: ['display_current_address']
        );
        /**
         * @var User
         */
        $user = User::find(auth()->user()->id);
        $deletePermission = $user->hasPermissionTo('Member: Delete In Any District') ||
        $user->hasPermissionTo('Member: Delete In Own District');
        if ($user->hasPermissionTo('Member: View In Any District') ) {
            $columns = $columns->addColumn(
                fields: ['name'],
                relation: 'district'
            );
        }
        $columns = $columns->addColumn(
            fields: ['name'],
            relation: 'taluk'
        )->addColumn(
            fields: ['name'],
            relation: 'village'
        );
        $columns = $columns->addActionColumn(
            viewRoute: 'members.show',
            deleteRoute: $this->getDestroyRoute(),
            deletePermission: $deletePermission,
        );

        return $columns->getRow();
    }

    public function getAdvanceSearchFields() {
        return [];
        $fields = [
            'name' => [
                'key' => 'name',
                'display_text' => 'Name',
                'input_val_type' => 'string',
                'input_elm_type' => 'text',
            ],
            'aadhaar_no' => [
                'key' => 'aadhaar_no',
                'display_text' => 'Aadhaar No.',
                'input_val_type' => 'string',
                'input_elm_type' => 'text',
            ],
            'membership_no' => [
                'key' => 'membership_no',
                'display_text' => 'Membership No.',
                'input_val_type' => 'string',
                'input_elm_type' => 'text',
            ],
            'taluk' => [
                'key' => 'taluk',
                'display_text' => 'Taluk',
                'input_val_type' => 'list_numeric',
                'input_elm_type' => 'select',
                'options' => Taluk::userAccessControlled()->get()->pluck('name', 'id'),
                'options_type' => 'key_value' //value_only
            ]
        ];

        $user = User::find(auth()->user()->id);
        if ($user->hasPermissionTo('Member: View In Any District')) {
            $fields['district'] = [
                'key' => 'district',
                'display_text' => 'District Office',
                'input_val_type' => 'list_numeric',
                'input_elm_type' => 'select',
                'options' => District::all()->pluck('name', 'id'),
                'options_type' => 'key_value' //value_only
            ];
        }

        return $fields;
    }

    public function getDownloadCols(): array
    {
        return [
            'id',
            'name'
        ];
    }
    public function getSearchPageData(): array
    {
        $data = array();
        $data['districts'] = District::UserAccessControlled()->withoutHo()
            ->get()->pluck('name', 'id');
        if (count($data['districts']) == 1) {
            $data['taluks'] = Taluk::inDistrict(
                array_keys($data['districts']->toArray())[0]
            )->get()->pluck('name', 'id');
        }
        return $data;
    }

    // public function getSearchFormElements()
    // {
    //     return [
    //         FormHelper::make
    //     ];
    // }

    public function show($id)
    {
        $m =  Member::with(
            [
                'district',
                'taluk',
                'village',
                'tradeUnion',
                'approvedBy',
                'feePayments' => function ($query) {
                    $query->orderBy('receipt_date', 'desc');
                },
                'nominees'
            ]
        )->where('id', $id)
            ->get()->first();
        if (!Gate::allows('view', $m)) {
            throw new Exception('You are not authorised to view this member');
        }
        return $m;
    }

    public function buildSearchFormLayout(Type $args)
    {
        # code...
    }

    public function getCreatePageData($aadhaarNo = null): array
    {
        $name = ucfirst(Str::lower($this->getModelShortName()));
        if (!$this->authoriseCreate()) {
            throw new AuthorizationException('The user is not authorised to view '.$name.'.');
        }

        return [
            'title' => 'Members',
            'form' => FormHelper::makeForm(
                title: 'Create Member',
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
            '_old' => [
                'aadhaar_no' => $aadhaarNo
            ]
        ];
    }

    public function getEditPageData($id): array
    {
        $member = Member::with('nominees')->where('id', $id)->get()->first();
        return [
            'title' => 'Members',
            '_old' => $member,
            'form' => FormHelper::makeEditForm(
                title: 'Edit Member',
                id: 'form_members_edit',
                action_route: 'members.update',
                action_route_params: ['id' => $id],
                success_redirect_route: 'members.show',
                items: $this->getEditFormElements($member),
                layout: $this->buildEditFormLayout(),
                label_position: 'top',
                width: 'full',
                type: 'easyadmin::partials.simpleform'
            )
        ];
    }

    public function getStoreValidationRules(): array
    {
        return [
            'membership_no' => ['sometimes','unique:members,membership_no'],
            'name' => ['required',],
            'name_mal' => ['sometimes',],
            'dobForEdit' => ['required', 'date_format:"d-m-Y"'],
            'gender' => ['required',],
            'marital_status' => ['required',],
            'mobile_no' => ['required',],
            'aadhaar_no' => ['required','unique:members,aadhaar_no'],
            'parent_guardian' => ['required',],
            'guardian_relationship' => ['required',],
            'current_address' => ['required_if:copy_address,false',],
            'current_address_mal' => ['sometimes',],
            'ca_pincode' => ['sometimes',],
            'copy_address' => ['sometimes'],
            'permanent_address' => ['required',],
            'permanent_address_mal' => ['sometimes',],
            'pa_pincode' => ['sometimes',],
            'districtOffice' => ['required',],
            'taluk' => ['required',],
            'village' => ['required',],
            'tradeUnion' => ['required',],
            'bank_acc_no' => ['required',],
            'bank_name' => ['required',],
            'bank_branch' => ['required',],
            'bank_ifsc' => ['required',],
            'photo' => (new EAInputMediaValidator())
                ->maxSize(200, 'kb')
                ->mimeTypes(['jpeg', 'jpg', 'png'])
                ->getRules(),
            'application_front' => (new EAInputMediaValidator())
                ->maxSize(200, 'kb')
                ->mimeTypes(['jpeg', 'jpg', 'png'])
                ->getRules(),
            'application_back' => (new EAInputMediaValidator())
                ->maxSize(200, 'kb')
                ->mimeTypes(['jpeg', 'jpg', 'png'])
                ->getRules(),
            'aadhaar_card' => (new EAInputMediaValidator())
                ->maxSize(200, 'kb')
                ->mimeTypes(['jpeg', 'jpg', 'png'])
                ->getRules(),
            'bank_passbook' => (new EAInputMediaValidator())
                ->maxSize(200, 'kb')
                ->mimeTypes(['jpeg', 'jpg', 'png'])
                ->getRules(),
            'ration_card' => (new EAInputMediaValidator())
                ->maxSize(200, 'kb')
                ->mimeTypes(['jpeg', 'jpg', 'png'])
                ->getRules(),
            'wb_passbook_front' => (new EAInputMediaValidator())
                ->maxSize(200, 'kb')
                ->mimeTypes(['jpeg', 'jpg', 'png'])
                ->getRules(),
            'wb_passbook_back' => (new EAInputMediaValidator())
                ->maxSize(200, 'kb')
                ->mimeTypes(['jpeg', 'jpg', 'png'])
                ->getRules(),
            'one_and_same_cert' => (new EAInputMediaValidator())
                ->maxSize(200, 'kb')
                ->mimeTypes(['jpeg', 'jpg', 'png'])
                ->getRules(),
            'nominees' => ['array', 'sometimes']
        ];
    }

    public function getUpdateValidationRules($id): array
    {
        $rules = $this->getStoreValidationRules();

        $rules['photo'] = ['sometimes'];
        $rules['application_front'] = ['sometimes'];
        $rules['application_back'] = ['sometimes'];
        $rules['aadhaar_card'] = ['sometimes'];
        $rules['bank_passbook'] = ['sometimes'];
        $rules['ration_card'] = ['sometimes'];
        $rules['wb_passbook_front'] = ['sometimes'];
        $rules['wb_passbook_back'] = ['sometimes'];
        $rules['one_and_same_cert'] = ['sometimes'];
        $rules['other_doc'] = ['sometimes'];
        $rules['is_approved'] = ['sometimes'];
        $rules['membership_no'] = ['required'];
        $rules['aadhaar_no'] = ['required', Rule::unique('members')->ignore($id)];
        $rules['reg_date'] = ['required', 'date_format:"d-m-Y"'];
        return $rules;
    }

    private function formElements(Member $member = null): array
    {
        $talukoptions = isset($member) ? Taluk::inDistrict($member->district_office_id)->get() : [];
        $villageoptions = isset($member) ? Village::inTaluk($member->taluk_id)->get() : [];
        $user = User::find(auth()->user()->id);

        $old = $this->request->input('ol');

        $showMembershipField = $old == 1;
        $data = [
            // 'membership_no_create' => FormHelper::makeInput(
            //     inputType: 'text',
            //     key: 'membership_no',
            //     label: 'Membership No.',
            //     properties: ['required' => true],
            //     show: $old == 1,
            //     formTypes: ['create']
            // ),
            'membership_no_create' => [
                'item_type' => 'input',
                'input_type' => 'inputs.membership-no',
                'key' => 'membership_no',
                'label' => 'Membership No.',
                'show' => $showMembershipField,
                'form_types' => ['create'],
                'properties' => ['required' => true],
                'authorised' => true
            ],
            'name' => FormHelper::makeInput(
                inputType: 'text',
                key: 'name',
                label: 'Name',
                properties: ['required' => true],
            ),
            'name_mal' => FormHelper::makeInput(
                inputType: 'text',
                key: 'name_mal',
                label: 'Name in Malayalam',
            ),
            'gender' => FormHelper::makeSelect(
                key: 'gender',
                label: 'Gender',
                options: config('generalSettings.genders'),
                options_type: 'value_only',
                properties: ['required' => true],
            ),
            'dob' => FormHelper::makeDatePicker(
                key: 'dobForEdit',
                label: 'Date of Birth',
                properties: ['required' => true,],
                startYear: 1947,
                endYear: Carbon::today()->year
            ),
            'marital_status' => FormHelper::makeSelect(
                key: 'marital_status',
                label: 'Marital Status',
                options: config('generalSettings.marital_status'),
                options_type: 'value_only',
                properties: ['required' => true],
            ),
            'mobile_no' => FormHelper::makeInput(
                inputType: 'text',
                key: 'mobile_no',
                label: 'Mobile No.',
                properties: ['required' => true],
            ),
            'aadhaar_no_display_create' => FormHelper::makeInput(
                inputType: 'text',
                key: 'aadhaar_no',
                label: 'Verified Aadhaar No.',
                properties: ['required' => true, 'disabled' => true],
                formTypes: ['create']
            ),
            'aadhaar_no_display_edit' => FormHelper::makeInput(
                inputType: 'text',
                key: 'aadhaar_no',
                label: 'Aadhaar No.',
                properties: ['required' => true],
                formTypes: ['edit']
            ),
            'aadhaar_no' => FormHelper::makeInput(
                inputType: 'hidden',
                key: 'aadhaar_no',
                properties: ['required' => true,],
                formTypes: ['create']
            ),
            // 'aadhaar_no' => FormHelper::makeInput(
            //     inputType: 'text',
            //     key: 'aadhaar_no',
            //     properties: ['required' => true,],
            //     formTypes: ['edit']
            // ),
            'membership_no' => FormHelper::makeInput(
                inputType: 'text',
                key: 'membership_no',
                label: 'Membership No.',
                properties: ['required' => true,],
                formTypes: ['edit']
            ),
            'reg_date' => FormHelper::makeInput(
                inputType: 'text',
                key: 'reg_date',
                label: 'Registration Date',
                properties: ['required' => true,],
                formTypes: ['edit']
            ),
            'ration_card_no' => FormHelper::makeInput(
                inputType: 'text',
                key: 'ration_card_no',
                label: 'Voters Id Card No.',
                properties: ['required' => true],
            ),
            'eshram_card_no' => FormHelper::makeInput(
                inputType: 'text',
                key: 'eshram_card_no',
                label: 'Eshram Card No.',
                properties: ['required' => true],
            ),
            'parent_guardian' => FormHelper::makeInput(
                inputType: 'text',
                key: 'parent_guardian',
                label: 'Parent/Guardian',
                properties: ['required' => true],
            ),
            'guardian_relationship' => FormHelper::makeSelect(
                key: 'guardian_relationship',
                label: 'Relationship',
                options: config('generalSettings.relationships'),
                options_type: 'value_only',
                properties: ['required' => true],
            ),
            'permanent_address' => FormHelper::makeTextarea(
                key: 'permanent_address',
                label: 'Permanent Address',
                properties: ['required' => true],
            ),
            'permanent_address_mal' => FormHelper::makeTextarea(
                key: 'permanent_address_mal',
                label: 'Permanent Address In Malayalam',
            ),
            'pa_pincode' => FormHelper::makeInput(
                inputType: 'text',
                key: 'pa_pincode',
                label: 'Permanent Addr. PIN Code',
            ),
            'residingDistrict' => FormHelper::makeSelect(
                key: 'residingDistrict',
                label: 'Residing District',
                options: District::all()->pluck('name', 'id'),
                options_type: 'key_value',
                properties: ['required' => true],
            ),
            'copy_address' => FormHelper::makeCheckbox(
                key: 'copy_address',
                label: 'Same as permanent address',
                fireInputEvent: true
            ),
            'current_address' => FormHelper::makeTextarea(
                key: 'current_address',
                label: 'Current Address',
                toggleOnEvents: ['copy_address' => [['==', true, false], ['==', false, true]]],
                properties: ['required' => true],
            ),
            'current_address_mal' => FormHelper::makeTextarea(
                key: 'current_address_mal',
                label: 'Current Address In Malayalam',
                toggleOnEvents: ['copy_address' => [['==', true, false], ['==', false, true]]],
            ),
            'ca_pincode' => FormHelper::makeInput(
                inputType: 'text',
                key: 'ca_pincode',
                label: 'Current Addr. PIN Code',
                toggleOnEvents: ['copy_address' => [['==', true, false], ['==', false, true]]],
            ),
            'district' => FormHelper::makeSelect(
                key: 'district',
                label: 'District',
                options: District::userAccessControlled()->get()->pluck('name', 'id'),
                options_type: 'key_value',
                properties: ['required' => true],
            ),
            'districtOffice' => FormHelper::makeSelect(
                key: 'districtOffice',
                label: 'District Office',
                options: District::userAccessControlled()->get()->pluck('name', 'id'),
                options_type: 'key_value',
                properties: ['required' => true],
                fireInputEvent: true
            ),
            'taluk' => FormHelper::makeSelect(
                key: 'taluk',
                label: 'Taluk',
                options: $talukoptions,
                options_src: [Self::class, 'getTaluks'],
                options_id_key: 'id',
                options_text_key: 'name',
                properties: ['required' => true],
                fireInputEvent: true,
                updateOnEvents: ['districtOffice'],
            ),
            'village' => FormHelper::makeSelect(
                key: 'village',
                label: 'Village',
                options: $villageoptions,
                options_src: [Self::class, 'getVillages'],
                options_id_key: 'id',
                options_text_key: 'name',
                properties: ['required' => true],
                updateOnEvents: ['taluk'],
                resetOnEvents: ['districtOffice']
            ),
            'bank_acc_no' => FormHelper::makeInput(
                inputType: 'text',
                key: 'bank_acc_no',
                label: 'Bank Account No.',
                properties: ['required' => true],
            ),
            'bank_name' => FormHelper::makeInput(
                inputType: 'text',
                key: 'bank_name',
                label: 'Name In Bank',
                properties: ['required' => true],
            ),
            'bank_branch' => FormHelper::makeInput(
                inputType: 'text',
                key: 'bank_branch',
                label: 'Bank Name & Branch',
                properties: ['required' => true],
            ),
            'bank_ifsc' => FormHelper::makeInput(
                inputType: 'text',
                key: 'bank_ifsc',
                label: 'IFSC code',
                properties: ['required' => true],
            ),
            'tradeUnion' => FormHelper::makeSelect(
                key: 'tradeUnion',
                label: 'Trade Union',
                options: TradeUnion::all()->pluck('name', 'id'),
                options_type: 'key_value',
                properties: ['required' => true],
                fireInputEvent: true
            ),
            'identification_mark_a' => FormHelper::makeInput(
                inputType: 'text',
                key: 'identification_mark_a',
                label: 'Identification Mark One',
                properties: ['required' => true],
            ),
            'identification_mark_b' => FormHelper::makeInput(
                inputType: 'text',
                key: 'identification_mark_b',
                label: 'Identification Mark Two',
                properties: ['required' => true],
            ),
            'work_locality' => FormHelper::makeInput(
                inputType: 'text',
                key: 'work_locality',
                label: 'Work Locality',
                properties: ['required' => true],
            ),
            'local_gov_body_type' => FormHelper::makeSelect(
                key: 'local_gov_body_type',
                label: 'Type of Local Gov. Body',
                options: config('generalSettings.local_gov_bodies'),
                options_type: 'value_only',
                properties: ['required' => true],
            ),
            'work_start_date' => FormHelper::makeDatePicker(
                key: 'work_start_date',
                label: 'Date of starting work as farm/agri labourer',
                properties: ['required' => true],
            ),
            'photo' => FormHelper::makeImageUploader(
                key: 'photo',
                label: 'Photo',
                properties: ['multiple' => false],
                theme: 'regular',
                allowGallery: false,
                validations: [
                    'max_size' => '200 kb',
                    'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                    ]
            ),
            'application_front' => FormHelper::makeImageUploader(
                key: 'application_front',
                label: 'Application Front Page',
                properties: ['multiple' => false],
                theme: 'regular',
                allowGallery: false,
                validations: [
                    'max_size' => '200 kb',
                    'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                ],
            ),
            'application_back' => FormHelper::makeImageUploader(
                key: 'application_back',
                label: 'Application Back Page',
                properties: ['multiple' => false],
                theme: 'regular',
                allowGallery: false,
                validations: [
                    'max_size' => '200 kb',
                    'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                    ]
            ),
            'aadhaar_card' => FormHelper::makeImageUploader(
                key: 'aadhaar_card',
                label: 'Aadhaar card',
                properties: ['multiple' => false],
                theme: 'regular',
                allowGallery: false,
                validations: [
                    'max_size' => '200 kb',
                    'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                    ]
            ),
            'bank_passbook' => FormHelper::makeImageUploader(
                key: 'bank_passbook',
                label: 'Bank Passbook',
                properties: ['multiple' => false],
                theme: 'regular',
                allowGallery: false,
                validations: [
                    'max_size' => '200 kb',
                    'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                    ]
            ),
            'ration_card' => FormHelper::makeImageUploader(
                key: 'ration_card',
                label: 'Ration Card',
                properties: ['multiple' => false],
                theme: 'regular',
                allowGallery: false,
                validations: [
                    'max_size' => '200 kb',
                    'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                    ]
            ),
            'wb_passbook_front' => FormHelper::makeImageUploader(
                key: 'wb_passbook_front',
                label: 'Member Passbook Front',
                properties: ['multiple' => false],
                theme: 'regular',
                allowGallery: false,
                validations: [
                    'max_size' => '200 kb',
                    'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                    ]
            ),
            'wb_passbook_back' => FormHelper::makeImageUploader(
                key: 'wb_passbook_back',
                label: 'Member Passbook Back',
                properties: ['multiple' => false],
                theme: 'regular',
                allowGallery: false,
                validations: [
                    'max_size' => '200 kb',
                    'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                    ]
            ),
            'one_and_same_cert' => FormHelper::makeImageUploader(
                key: 'one_and_same_cert',
                label: 'One And Same Certificate',
                properties: ['multiple' => false],
                theme: 'regular',
                allowGallery: false,
                validations: [
                    'max_size' => '200 kb',
                    'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                    ]
            ),
            'other_doc' => FormHelper::makeImageUploader(
                key: 'other_doc',
                label: 'Any Other Document',
                properties: ['multiple' => false],
                theme: 'regular',
                allowGallery: false,
                validations: [
                    'max_size' => '200 kb',
                    'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                    ]
            ),
            'nominees' => FormHelper::makeDynamicInput(
                key: 'nominees',
                label: 'Nominees',
                component: 'inputs.member-nominees'
            ),
            'is_approved' => FormHelper::makeCheckbox(
                key: 'is_approved',
                label: 'Is Approved?',
                // toggle: true,
                displayText: ['Yes', 'No'],
                show: $user->hasPermissionTo('Member: Approve In Own District')
            )
        ];

        if ($member != null) {
            unset($data['photo']['validations']['max_size']);
            unset($data['application_front']['validations']['max_size']);
            unset($data['application_back']['validations']['max_size']);
            unset($data['aadhaar_card']['validations']['max_size']);
            unset($data['bank_passbook']['validations']['max_size']);
            unset($data['ration_card']['validations']['max_size']);
            unset($data['wb_passbook_front']['validations']['max_size']);
            unset($data['wb_passbook_back']['validations']['max_size']);
            unset($data['one_and_same_cert']['validations']['max_size']);
            unset($data['other_doc']['validations']['max_size']);
        }

        return $data;
    }

    public function authoriseCreate()
    {
        $u = User::find(auth()->user()->id);
        return $u->hasPermissionTo('Member: Create In Any District') ||
            $u->hasPermissionTo('Member: Create In Own District');
    }

    private function getQuery()
    {
        return $this->modelClass::query()
            ->userAccessControlled()
            ->with(
                [
                    'district' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'taluk' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'village' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'tradeUnion' => function ($query) {
                        $query->select('id', 'name');
                    },
                ]
            );
    }

    protected function relations(): array
    {
        return [
            'district' => [
                'search_column' => 'id',
                'filter_column' => 'id',
                'sort_column' => 'id',
            ],
            'residingDistrict' => [
                'search_column' => 'id',
                'filter_column' => 'id',
                'sort_column' => 'id',
            ],
            'districtOffice' => [
                'search_column' => 'id',
                'filter_column' => 'id',
                'sort_column' => 'id',
            ],
            'taluk' => [
                'search_column' => 'id',
                'filter_column' => 'id',
                'sort_column' => 'id',
                // 'search_fn' => function ($query, $op, $val) {
                //     $query->whereHas('roles', function ($q) use ($op, $val) {
                //         $q->where('name', $op, $val);
                //     });
                // }
            ],
            'village' => [
                'search_column' => 'id',
                'filter_column' => 'id',
                'sort_column' => 'id',
            ],
            'tradeUnion' => [
                'search_column' => 'id',
                'filter_column' => 'id',
                'sort_column' => 'id',
            ],
            'nominees' => [
                'search_column' => 'id',
                'filter_column' => 'id',
                'sort_column' => 'id',
            ]
        ];
    }

    public function buildCreateFormLayout(): array
    {
        $layout = (new ColumnLayout())
            ->addElements(
                [
                    (new RowLayout(width: '1/4'))->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('membership_no_create'),
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('aadhaar_no_display_create'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('aadhaar_no'),
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Personal Info')),
                    (new RowLayout(
                        width: 'full'
                    ))->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/2'
                            ))->addInputSlot('name'),
                            (new ColumnLayout(
                                width: '1/2'
                            ))->addInputSlot('name_mal'),
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('dob'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('gender'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('marital_status')
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('mobile_no'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('parent_guardian'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('guardian_relationship')
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Permanent Address')),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('permanent_address'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('permanent_address_mal'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('pa_pincode'),
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Current Address')),
                    (new RowLayout(width: 'full'))->addInputSlot('copy_address'),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('current_address'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('current_address_mal'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('ca_pincode'),
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Office Info')),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('districtOffice'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('taluk'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('village'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('tradeUnion'),
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Bank Account Info')),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('bank_acc_no'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('bank_name'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('bank_branch'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('bank_ifsc')
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Nominees')),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: 'full'
                            ))->addInputSlot('nominees'),
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Image Uploads')),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('photo'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('application_front'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('application_back'),
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('aadhaar_card'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('bank_passbook'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('ration_card'),
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('wb_passbook_front'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('wb_passbook_back'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('one_and_same_cert'),
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('other_doc'),
                        ]
                    ),
                ]
            );

        return $layout->getLayout();
    }

    public function buildEditFormLayout(): array
    {
        $layout = (new ColumnLayout())
            ->addElements(
                [
                    (new RowLayout(width: '1/4'))->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('aadhaar_no_display_edit'),
                            // (new ColumnLayout(
                            //     width: '1/4'
                            // ))->addInputSlot('aadhaar_no'),
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('membership_no'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('reg_date'),
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Personal Info')),
                    (new RowLayout(
                        width: 'full'
                    ))->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/2'
                            ))->addInputSlot('name'),
                            (new ColumnLayout(
                                width: '1/2'
                            ))->addInputSlot('name_mal'),
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('dob'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('gender'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('marital_status')
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('mobile_no'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('parent_guardian'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('guardian_relationship')
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Permanent Address')),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('permanent_address'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('permanent_address_mal'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('pa_pincode'),
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Current Address')),
                    (new RowLayout(width: 'full'))->addInputSlot('copy_address'),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('current_address'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('current_address_mal'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('ca_pincode'),
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Office Info')),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('districtOffice'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('taluk'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('village'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('tradeUnion'),
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Bank Account Info')),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('bank_acc_no'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('bank_name'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('bank_branch'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('bank_ifsc')
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Nominees')),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: 'full'
                            ))->addInputSlot('nominees'),
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Image Uploads')),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('photo'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('application_front'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('application_back'),
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('aadhaar_card'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('bank_passbook'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('ration_card'),
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('wb_passbook_front'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('wb_passbook_back'),
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('one_and_same_cert'),
                        ]
                    ),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('other_doc'),
                        ]
                    ),
                    (new RowLayout(null, $style = "background-color: rgba(255, 255, 0, 0.5); font-weight: bold; padding: 10px 0 10px 0;"))->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/3'
                            ))->addInputSlot('is_approved'),
                        ]
                    ),
                ]
            );

        return $layout->getLayout();

    }

    // public function buildCreateFormLayout(): array
    // {
    //     $layout = (new ColumnLayout())
    //         ->addElements(
    //             [
    //                 (new RowLayout(width: 'full'))
    //                     ->addElement(new SectionDivider('Personal Info')),
    //                 (new RowLayout(
    //                     width: 'full'
    //                 ))->addElements(
    //                     [
    //                         (new ColumnLayout(
    //                             width: '1/2'
    //                         ))->addInputSlot('name'),
    //                         (new ColumnLayout(
    //                             width: '1/2'
    //                         ))->addInputSlot('name_mal'),
    //                     ]
    //                 ),
    //                 (new RowLayout())->addElements(
    //                     [
    //                         (new ColumnLayout(
    //                             width: '1/3'
    //                         ))->addInputSlot('dob'),
    //                         (new ColumnLayout(
    //                             width: '1/3'
    //                         ))->addInputSlot('gender'),
    //                         (new ColumnLayout(
    //                             width: '1/3'
    //                         ))->addInputSlot('marital_status')
    //                     ]
    //                 ),
    //                 (new RowLayout())->addElements(
    //                     [
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('mobile_no'),
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('aadhaar_no'),
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('ration_card_no'),
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('eshram_card_no')
    //                     ]
    //                 ),
    //                 (new RowLayout())->addElements(
    //                     [
    //                         (new ColumnLayout(
    //                             width: '1/2'
    //                         ))->addInputSlot('parent_guardian'),
    //                         (new ColumnLayout(
    //                             width: '1/2'
    //                         ))->addInputSlot('guardian_relationship'),
    //                     ]
    //                 ),
    //                 (new RowLayout(width: 'full'))
    //                     ->addElement(new SectionDivider('Address')),
    //                 (new RowLayout())->addElements(
    //                     [
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('current_address'),
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('current_address_mal'),
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('ca_pincode'),
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('residingDistrict'),
    //                     ]
    //                 ),
    //                 (new RowLayout())->addElements(
    //                     [
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('permanent_address'),
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('permanent_address_mal'),
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('pa_pincode'),
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('district'),
    //                     ]
    //                 ),
    //                 (new RowLayout())->addElements(
    //                     [
    //                         (new ColumnLayout(
    //                             width: '1/3'
    //                         ))->addInputSlot('districtOffice'),
    //                         (new ColumnLayout(
    //                             width: '1/3'
    //                         ))->addInputSlot('taluk'),
    //                         (new ColumnLayout(
    //                             width: '1/3'
    //                         ))->addInputSlot('village'),
    //                     ]
    //                 ),
    //                 (new RowLayout(width: 'full'))
    //                     ->addElement(new SectionDivider('Bank Account Info')),
    //                 (new RowLayout())->addElements(
    //                     [
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('bank_acc_no'),
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('bank_name'),
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('bank_branch'),
    //                         (new ColumnLayout(
    //                             width: '1/4'
    //                         ))->addInputSlot('bank_ifsc')
    //                     ]
    //                 ),
    //                 (new RowLayout(width: 'full'))
    //                     ->addElement(new SectionDivider('Work & Identification Info')),
    //                 (new RowLayout())->addElements(
    //                     [
    //                         (new ColumnLayout(
    //                             width: '1/3'
    //                         ))->addInputSlot('tradeUnion'),
    //                         (new ColumnLayout(
    //                             width: '1/3'
    //                         ))->addInputSlot('identification_mark_a'),
    //                         (new ColumnLayout(
    //                             width: '1/3'
    //                         ))->addInputSlot('identification_mark_b'),
    //                     ]
    //                 ),
    //                 (new RowLayout())->addElements(
    //                     [
    //                         (new ColumnLayout(
    //                             width: '1/3'
    //                         ))->addInputSlot('work_locality'),
    //                         (new ColumnLayout(
    //                             width: '1/3'
    //                         ))->addInputSlot('local_gov_body_type'),
    //                         (new ColumnLayout(
    //                             width: '1/3'
    //                         ))->addInputSlot('work_start_date'),
    //                     ]
    //                 ),
    //                 (new RowLayout(width: 'full'))
    //                     ->addElement(new SectionDivider('Image Uploads')),
    //                 (new RowLayout())->addElements(
    //                     [
    //                         (new ColumnLayout(
    //                             width: '1/2'
    //                         ))->addInputSlot('aadhaar_card'),
    //                         (new ColumnLayout(
    //                             width: '1/2'
    //                         ))->addInputSlot('bank_passbook'),
    //                     ]
    //                 ),
    //             ]
    //         );

    //     return $layout->getLayout();
    // }

    public function getTaluks($data)
    {
        // if (isset($data['depended_id'])) {
        //     $taluks = Taluk::where('name', 'like', $data['search'].'%')
        //         ->where('district_id', $data['depended_id'])->get();
        // } else {
        //     $taluks = [];
        // }
        $taluks = Taluk::where('district_id', $data['depended_id'])->get();
        return new InputUpdateResponse(
            result: $taluks,
            message: "success",
            isvalid: true
        );
    }

    public function getVillages($data)
    {
        // if (isset($data['depended_id'])) {
        //     $villages = Village::where('name', 'like', $data['search'].'%')
        //         ->where('taluk_id', $data['depended_id'])->get();
        // } else {
        //     $villages = [];
        // }
        $villages = Village::where('taluk_id', $data['depended_id'])->get();

        return new InputUpdateResponse(
            result: $villages,
            message: "success",
            isvalid: true
        );
    }

    public function processBeforeStore(array $data): array
    {
        $data['membership_no'] = $data['membership_no'] ?? AppHelper::getMembershipNumber(
            $data['districtOffice'],
            $data['taluk'],
            $data['village']
        );
        $data['district'] = $data['districtOffice'];
        if (filter_var($data['copy_address'], FILTER_VALIDATE_BOOLEAN)) {
            $data['current_address'] = $data['permanent_address'];
            $data['current_address_mal'] = $data['permanent_address_mal'];
            $data['ca_pincode'] = $data['pa_pincode'];
        }
        /**
         * @var User
         * */
        $user = User::find(auth()->user()->id);
        if ($user->hasPermissionTo('Member: Approve In Own District') ) {
            $data['approved_by'] = $user->id;
            $data['approved_at'] = Carbon::now()->format('Y-m-d');
        }
        unset($data['copy_address']);
        unset($data['is_approved']);

        $data['created_by'] = auth()->user()->id;
        // $data['dob'] = AppHelper::formatDateForSave($data['dob']);
        $data['dob'] = AppHelper::formatDateForSave($data['dobForEdit']);
        unset($data['dobForEdit']);
        $data['reg_date'] = Carbon::now()->format('Y-m-d');
        return $data;
    }

    public function processBeforeUpdate(array $data, $id = null): array
    {
        if (filter_var($data['copy_address'], FILTER_VALIDATE_BOOLEAN)) {
            $data['current_address'] = $data['permanent_address'];
            $data['current_address_mal'] = $data['permanent_address_mal'];
            $data['ca_pincode'] = $data['pa_pincode'];
        }

        /**
         * @var Member
         */
        $member = Member::find($id);
        /**
         * @var User
         * */
        $user = User::find(auth()->user()->id);
        if ($user->cannot('approve', $member)) {
            info('User unatuthoried to approve member');
        } elseif (!in_array($data['is_approved'], [0, false, 'false', 'no', 'False', 'No'])) {
            $data['approved_by'] = $user->id;
            $data['approved_at'] = Carbon::createFromTimestamp(time())->format('Y-m-d H:i:s');
        } elseif (in_array($data['is_approved'], [0, false, 'false', 'no', 'False', 'No'])) {
            $data['approved_by'] = null;
            $data['approved_at'] = null;
        } else {
            info('unknown approval status!');
        }

        unset($data['copy_address']);
        unset($data['is_approved']);

        $data['created_by'] = auth()->user()->id;
        $data['dob'] = AppHelper::formatDateForSave($data['dobForEdit']);
        unset($data['dobForEdit']);
        $data['reg_date'] = AppHelper::formatDateForSave($data['reg_date']);;
        return $data;
    }

    public function suggestionslist($data, $onlyVerified = false)
    {
        if (isset($data['exact']) && $data['exact'] == 'true') {
            $members = Member::userAccessControlled()
                ->with(['taluk', 'village'])
                ->where('membership_no', '=', trim($data['membership_no']))
                ->limit(20)
                ->get();
        } else {
            $members = Member::userAccessControlled()
                ->with(['taluk', 'village'])
                ->where('membership_no', 'like', trim($data['membership_no']).'%')
                ->limit(20)
                ->get();
        }
        return $members;
    }

    public function fetch($id)
    {
        return Member::userAccessControlled()
            ->with(['taluk', 'village', 'feePayments' =>  fn ($query) => $query->orderBy('receipt_date', 'asc')])
            ->where('id', $id)
            ->get()->first();
    }

    public function fetchMemberCurl($membershipNo, $memberId)
    {
        info('params');
        info($membershipNo);
        info($memberId);
        if ($memberId == null) {
            info('checking existing member');
            $m = Member::where('membership_no', $membershipNo)->get()->first();
            info($m);
            if ($m != null) {
                return [
                    'success' => false,
                    'exists' => true,
                    'member' => $m
                ];
            }
        }
        $ch = curl_init();
        $token ='$asajdas/as7%26dda67ada%23423AHJ';
        $url = "https://api.karshakathozhilali.org/fetch-member?membership_no=$membershipNo&security_token=$token";
        info($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);

        $response = curl_exec($ch);


        info($response);
        $data = json_decode($response);
        $data = $data->member;
        info('name >>>>>>>>>>>>>>');
        info($data->name);
        $responseData = [];


        if(curl_error($ch)){
            return [
                'success' => false,
                'error' => curl_error($ch)
            ];
        }else{
            $responseData = [
                'success' => true,
            ];
        }


        curl_close($ch);

        try {
            DB::beginTransaction();
            if ($memberId != null) {
                $member = Member::find($memberId);
                info('existing member:');
                info($member);
            } else {
                info('new member created');
                $member = new Member();
                $member->membership_no = $data->membership_no;
                $member->reg_date = $data->reg_date;
                $member->district_id = $this->getDistrictIdForOldId($data->get_district->id);
                $member->taluk_id = $data->get_taluk->id;
                $member->village_id = $data->get_village->id;
                $member->gender = $data->gender;
                $member->dob = $data->dob;
                $member->marital_status = $data->marital_status;
                $member->parent_guardian = $data->parent_spouse_name;
                $member->guardian_relationship = $data->relationship;
                $member->religion_id = $data->religion_id;
                $member->caste_id = $data->caste_id;
                $member->trade_union_id = $data->trade_union_id;
                $c_at = explode('T', $data->created_at)[0];
                info('c-at');
                info($c_at);
                info($data->created_at);
                info('compare dates:');
                info($data->created_at == '-000001-11-29T18:06:32.000000Z');
                $member->created_at = $data->created_at == '' || $data->created_at == '-000001-11-29T18:06:32.000000Z' || $data->created_at == null ? Carbon::today()->format('Y-m-d') : $c_at;
                $member->approved_at = $data->created_at == '' || $data->created_at == '-000001-11-29T18:06:32.000000Z' || $data->created_at == null ? Carbon::today()->format('Y-m-d') : $c_at;
                $member->created_by = auth()->user()->id;
            }
            //Member Name, Member Address, Aadhaar Number, Phone Number, Bank Information, Subscription Details, Images
            $member->name = $data->name;
            $member->aadhaar_no = $data->aadhaar_no;
            $member->mobile_no = $data->mobile_no;
            $member->current_address = $data->present_address;
            $member->permanent_address = $data->permanent_address;
            $member->live_id = $data->id;
            $member->bank_name = $data->name_in_bank;
            $member->bank_branch = $data->bank_name;
            $member->bank_acc_no = $data->bank_ac_no;
            $member->bank_ifsc = $data->ifsc_code;
            $member->merged = 1;
            $member->save();
info('member saved');
            foreach ($data->subscriptions as $s) {
                //get matching fee_type_id for subscription_type_id
                $feeTypeId = config('generalSettings.fee_types_map')[$s->subscription_type_id];
                //create feeCollection
                $fc = new FeeCollection();
                $fc->member_id = $member->id;
                $fc->district_id = $member->district_id;
                $fc->book_number = $s->book_number;
                $fc->receipt_number = $s->voucher_number;
                $fc->receipt_date = $s->subscription_date;
                $fc->created_at = $s->created_at;
                $fc->notes = $s->notes. ' '.$s->payment_through;

                $feeItemsData = $this->getFeeItemsForOld($s);

                $fc->total_amount = $feeItemsData['total'];

                $fc->save();

                foreach ($feeItemsData['items'] as $fi) {
                    $item = new FeeItem();
                    $item->fee_collection_id = $fc->id;
                    $item->fee_type_id = $fi['fee_type_id'];
                    $item->amount = $fi['amount'];
                    if (in_array($feeTypeId, config('generalSettings.fee_types_with_tenure'))) {
                        $item->period_from = $fi['from'];
                        $item->period_to = $fi['to'];
                        $item->tenure = $fi['tenure'];
                    }
                    $item->save();
                }

                $this->createMediaForOld($member, 'aadhaar_file', $data->aadhaar_file);
                $this->createMediaForOld($member, 'bank_passbook_file', $data->bank_passbook_file);
                $this->createMediaForOld($member, 'wf_front', $data->wf_front);
                $this->createMediaForOld($member, 'wf_back', $data->wf_back);
                $this->createMediaForOld($member, 'one_certificate', $data->one_certificate);
                //check if fee_type_id in types_with_tenures
                // if yes, add tenures
            }
            DB::commit();
            info('committed');
        } catch (\Throwable $e) {
            info($e->__toString());
            DB::rollBack();
            return [
                'success' => false,
                'error' => $e->__toString()
            ];
        }
        //report-data-merge
/*
        $reportUrl = "https://api.karshakathozhilali.org/report-data-merge?membership_no=$membershipNo&security_token=$token";
        info($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $reportUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);

        $r = curl_exec($ch);
*/
        return $responseData;
    }

    private function createMediaForOld($member, $prop, $url)
    {
        if($url == '' || strlen($url) == 0 || $url == null) {
            return false;
        }
        $property = '';
        switch($prop) {
            case 'aadhaar_file':
                $property = 'aadhaar_card';
                break;
            case 'bank_passbook_file':
                $property = 'bank_passbook';
                break;
            case 'wf_front':
                $property = 'wb_passbook_front';
                break;
            case 'wf_back':
                $property = 'wb_passbook_back';
                break;
            case 'one_certificate':
                $property = 'one_and_same_cert';
                break;
        }
        $member->addOneImageFromUrl($property, $url);
    }

    private function getFeeItemsForOld($subscription)
    {
        //fee_type_id, amount, from, to, tenure (annual_sub id-2)

        $total = $subscription->amount + $subscription->fine + $subscription->arrears;
        $items = [];
        $feeTypeId = config('generalSettings.fee_types_map')[$subscription->subscription_type_id];
        $hasTenure = in_array($feeTypeId, config('generalSettings.fee_types_with_tenure'));
        $tenure = '';
        if ($hasTenure) {
            $tenure = $subscription->tenure ??  $subscription->period_from . ' to ' .$subscription->period_to;
        }

        $items[] = [
            'fee_type_id' => $feeTypeId,
            'amount' => $subscription->amount,
            'from' => $hasTenure ? $subscription->period_from : null,
            'to' => $hasTenure ? $subscription->period_to : null,
            'tenure' => $tenure
        ];

        if ($subscription->fine > 0) {
            $items[] = [
                'fee_type_id' => 3,
                'amount' => $subscription->fine,
                'from' => null,
                'to' => null,
                'tenure' => ''
            ];
        }
        if ($subscription->arrears > 0) {
            $items[] = [
                'fee_type_id' => 4,
                'amount' => $subscription->arrears,
                'from' => null,
                'to' => null,
                'tenure' => ''
            ];
        }
        return [
            'items' => $items,
            'total' => $total
        ];
    }

    private function getDistrictIdForOldId($did)
    {
        return config('generalSettings.districts_idmap')[$did];
    }

    public function annualFeesPeriod($id, $months): array
    {
        // $m = Member::find($id);
        // $fc = $m->feeItems()->where('fee_type_id', 1)->get();
        $fi = DB::table('members as m')->join(
            'fee_collections as fc', 'm.id', '=', 'fc.member_id'
        )->join(
            'fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id'
        )->join(
            'fee_types as ft', 'ft.id', '=', 'fi.fee_type_id'
        )->select('m.id', 'fc.id', 'fc.receipt_date', 'fi.*')
            ->where('m.id', $id)
            ->where('ft.name', 'like', 'Annual Subscription')
            ->orderBy('fi.period_to', 'desc')
            ->get()->first();
        // dd($fi->period_to);
        if ($fi != null) {
            $lastPaid = Carbon::parse($fi->period_to);
            $from = $lastPaid;
            $to = $lastPaid;
            $from = $lastPaid->addDay()->startOfMonth()
                ->format('d-m-Y');
            $to = $lastPaid->subDay()->addMonths($months)
                ->endOfMonth()->format('d-m-Y');
            $result = [
                'from' => $from,
                'to' => $to,
            ];
            // dd($fc->period_to, $result);
            return $result;
        }
        return [
            'from' => null,
            'to' => null,
        ];
    }

    public function storeFeesCollection($id, $data, $old = false)
    {
        if (isset($data['receipt_number'])) {
            $qstr = 'SELECT * FROM fee_collections WHERE receipt_number = '
            . '\''.$data['book_number'].'/'.$data['receipt_number'].'\'';
            info($qstr);
            $results = DB::select($qstr);
            if (count($results) > 0) {
                return [
                    'success' => false,
                    'message' => 'The receipt number has already been taken.',
                    'errors' => array(
                        'receipt_number' => 'The receipt number has already been taken.'
                    )
                ];
            }
        }
        $member = Member::find($id);
        $district = $member->district;
        if ($member == null) {
            return [ '' => false ];
        }
        try {
            $updateLastReceiptNo = true;
            if(isset($data['receipt_number'])) {
                // info($data['receipt_number']);
                $updateLastReceiptNo = false;
            }
            DB::beginTransaction();
            $bookNo = $data['book_number'] ?? AppHelper::getBookNumber($district);
            $receiptNo = isset($data['receipt_number']) ? $bookNo.'/'.$data['receipt_number'] : AppHelper::getReceiptNumber($district);
            $fc = FeeCollection::create([
                'member_id' => $member->id,
                'district_id' => $member->district_id,
                'book_number' => $bookNo,
                'receipt_number' => $receiptNo,
                'total_amount' => 0,
                'receipt_date' => AppHelper::formatDateForSave($data['date']),
                'payment_mode_id' => config('generalSettings.default_payment_mode_id'),
                'collected_by' => auth()->user()->id,
                'notes' => $data['notes'] ?? '',
                // 'period_from' => AppHelper::formatDateForSave($data['period_from']),
                // 'period_to' => AppHelper::formatDateForSave($data['period_to']),
                // 'tenure' => $data['period_from'] . ' to ' . $data['period_to'],
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

            $sum = 0;
            foreach ($data['fee_item'] as $item) {
                $sum += $item['amount'];
                $fiData = [
                    'fee_collection_id' => $fc->id,
                    'fee_type_id' => $item['fee_type_id'],
                    'amount' => $item['amount'],
                ];
                if (isset($item['period_from'])) {
                    $fiData['period_from'] = AppHelper::formatDateForSave(thedate: $item['period_from'], setTimeTo: 'start');
                }
                if (isset($item['period_to'])) {
                    $fiData['period_to'] = AppHelper::formatDateForSave(thedate: $item['period_to'], setTimeTo: 'end');
                }
                if (isset($item['period_from']) && isset($item['period_to'])) {
                    $fiData['tenure'] = Carbon::createFromFormat('d-m-Y', $item['period_from'])
                        ->format('m-Y')
                        . ' to '
                        . Carbon::createFromFormat('d-m-Y', $item['period_to'])->format('m-Y');
                }
                FeeItem::create($fiData);
            }

            $fc->refresh();
            $fc->total_amount = $sum;
            $fc->save();
            DB::commit();

            if ($updateLastReceiptNo) {
                FeeCollectionEvent::dispatch(
                    $district->id,
                    $fc,
                    FeeCollectionEvent::$ACTION_CREATED
                );
            }

            BusinessActionEvent::dispatch(
                FeeCollection::class,
                $fc->id,
                'Created',
                auth()->user()->id,
                null,
                $fc,
                'Created Receipt. No.: '.$fc->receipt_number,
            );

            $receipt = FeeCollection::with(
                'feeItems', 'collectedBy', 'member', 'paymentMode'
            )->where('id', $fc->id)->get()->first();
            return [
                'success' => true,
                'receipt' => $receipt
            ];
        } catch (\Throwable $e) {
            info("couldn't create the receipt. error:");
            info($e->__toString());
            DB::rollback();
            return [
                'success' => false
            ];
        }

    }

    public function storeBulkFees($data)
    {
        $sum = 0;
        $fiData = [];
        foreach ($data['fee_item'] as $item) {
            $sum += $item['amount'];
            $fi = [
                'fee_collection_id' => '',
                'fee_type_id' => $item['fee_type_id'],
                'amount' => $item['amount'],
            ];
            if (isset($item['period_from'])) {
                $fi['period_from'] = AppHelper::formatDateForSave($item['period_from']);
            }
            if (isset($item['period_to'])) {
                $fi['period_to'] = AppHelper::formatDateForSave($item['period_to']);
            }
            if (isset($item['period_from']) && isset($item['period_to'])) {
                $fi['tenure'] = $item['period_from'] . ' to ' . $item['period_to'];
            }
            $fiData[] = $fi;
            // FeeItem::create($fiData);
        }
        $mids = explode(',', $data['members']);
        $successList = [];
        $fcIds = [];
        $success = true;
        foreach ($mids as $id) {
            if ($id == "" || intval($id) == 0) {
                continue;
            }
            $member = Member::find($id);
            // info('__mid: '.$id);
            // info('__member: '.$member);
            $attemptSuccess = false;
            try {
                DB::beginTransaction();
                $bookNo = AppHelper::getBookNumber($member->district_id);
                $receiptNo = AppHelper::getReceiptNumber($member->district_id);

                $fc = FeeCollection::create([
                    'member_id' => $id,
                    'district_id' => $member->district_id,
                    'book_number' => $bookNo,
                    'receipt_number' => $receiptNo,
                    'total_amount' => $sum,
                    'receipt_date' => AppHelper::formatDateForSave($data['date']),
                    'payment_mode_id' => config('generalSettings.default_payment_mode_id'),
                    'collected_by' => auth()->user()->id,
                    'notes' => $data['notes'] ?? '',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]);

                foreach ($fiData as $fid) {
                    $fid['fee_collection_id'] = $fc->id;
                    FeeItem::create($fid);
                }

                info('Receipt created');
                DB::commit();
                info('Receipt created');
                $successList[] = $id;
                $fcIds[] = $fc->id;
                // $attemptSuccess = true;
                FeeCollectionEvent::dispatch(
                    $member->district_id,
                    $fc,
                    FeeCollectionEvent::$ACTION_CREATED
                );

                BusinessActionEvent::dispatch(
                    FeeCollection::class,
                    $fc->id,
                    'Created',
                    auth()->user()->id,
                    null,
                    $fc,
                    'Created Receipt. No.: '.$fc->receipt_number,
                );

            } catch (\Throwable $e) {
                // info("Couldn't create bulk receipts. Failed at id: ".$id);
                // info($e->__toString());
                DB::rollback();
                $success = false;
            }
            // if ($attemptSuccess) {
            //     FeeCollectionEvent::dispatch(
            //         $member->district_id,
            //         $fc,
            //         FeeCollectionEvent::$ACTION_CREATED
            //     );

            //     BusinessActionEvent::dispatch(
            //         FeeCollection::class,
            //         $fc->id,
            //         'Created',
            //         auth()->user()->id,
            //         null,
            //         $fc,
            //         'Created Receipt. No.: '.$fc->receipt_number,
            //     );
            // }
        }

        if ($success) {
            return [
                'success' => true,
                'fc_ids' => implode(",", $fcIds),
                'receipts' => FeeCollection::whereIn('id', $fcIds)
                    ->with(['member', 'feeItems'])->get(),
                'success_list' => implode(",", $successList)
            ];
        } else {
            return [
                'success' => false,
                'success_list' => implode(",", $successList)
            ];
        }
    }

    public function verifyAadhaar($aadhaarNo)
    {
        $aadhaarNo = str_replace(' ', '', $aadhaarNo);

        if (strlen($aadhaarNo) < 12) {
            return [
                'status' => 'Error',
                'message' => 'Aadhaar number shall have 12 digits'
            ];
        } else {
            $ch = curl_init();
            $headers = array(
                'Accept: application/json',
                'Content-Type: application/json',
            );
            curl_setopt(
                $ch,
                CURLOPT_URL,
                'https://aiis.lc.kerala.gov.in/index.php/aadharcheck/'.$aadhaarNo
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $val = curl_exec($ch);

            $result = json_decode($val);
            //Exists in Kerala Agricultural Workers Welfare Fund Board
            $status = $result->Status;
            $message = $result->Message;

            if (trim($status) == 'AVAILED' && trim($message) == 'Exists in Kerala Agricultural Workers Welfare Fund Board') {
                $member = Member::where('aadhaar_no', $aadhaarNo)->get()->first();
                if ($member == null) {
                    $status = 'NOT AVAILED';
                    $message = 'Old data';
                } else {
                    $status = 'AVAILED';
                    $message = 'Cannot add member with this aadhaar number. Aadhaar number already present in database. Search and edit the member instead.';
                }
            }
            return [
                'status' => $status,
                'message' => $message
            ];
        }
    }

    public function unapprovedMembers($data)
    {
        return Member::userAccessControlled()->unapproved()
            ->paginate(
                perPage: 100,
                page: $data['page'] ?? 1
            );
    }

    public function processAfterStore($instance): void
    {
        BusinessActionEvent::dispatch(
            Member::class,
            $instance->id,
            'Created',
            auth()->user()->id,
            null,
            $instance,
            'Created Member with Membership No.: '.$instance->membership_no,
        );
    }

    public function processAfterUpdate($oldInstance, $instance): void
    {
        $action = "Updated";
        if ($oldInstance->approved_at == null && $instance->approved_at != null) {
            $action = "Approved";
        }
        BusinessActionEvent::dispatch(
            Member::class,
            $instance->id,
            $action,
            auth()->user()->id,
            $oldInstance,
            $instance,
            $action.' Member with Membership No.: '.$instance->membership_no,
        );
    }

    public function authoriseDestroy($member): bool
    {
        $mayDelete = $member->feePayments()->count() == 0 &&
            $member->allowances()->count() ==0;
        if ($mayDelete) {
            throw new AuthorizationException('Can\'t delete the Member. There may be receipts or allowances issued to this member.');
        }
        $user = auth()->user();
        if ($user->hasPermissionTo('Member: Delete In Any District') ||
            $user->hasPermissionTo('Member: Delete In Own District') && $member->district_id == $user->district_id) {
            return true;
        }
        throw new AuthorizationException('Unable to delete the Member. The user is not authorised for this action.');
    }

    public function report(
        int $itemsCount,
        ?int $page,
        array $searches,
        array $sorts,
        array $filters,
        array $advParams,
        bool $indexMode,
        string $selectedIds = '',
        string $resultsName = 'results',
    ): array {
        $name = ucfirst(Str::plural(Str::lower($this->getModelShortName())));
        if (!$this->authoriseIndex()) {
            throw new AuthorizationException('The user is not authorised to view '.$name.'.');
        }
        $this->preIndexExtra();

        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advParams
        );

        if ($indexMode
            || count($searches) > 0
            || count($sorts) > 0
            || count($filters) > 0
            || count($advParams) > 0
        ) {
            // if (!$this->sqlOnlyFullGroupBy) {
            //     DB::statement("SET SQL_MODE=''");
            // }

            $results = $queryData['query']->orderBy(
                $this->orderBy[0],
                $this->orderBy[1]
            )->paginate(
                $itemsCount,
                $this->selects,
                'page',
                $page
            );

            // if (!$this->sqlOnlyFullGroupBy) {
            //     DB::statement("SET SQL_MODE='only_full_group_by'");
            // }

            $this->postIndexExtra();
            $data = $results->toArray();
        } else {
            $data = [];
        }
        // $paginator = $this->getPaginatorArray($results);
        return [
            'results' => $results,
            // 'results_json' => json_encode($this->formatIndexResults($results->toArray()['data'])),
            'searches' => $queryData['searchParams'],
            'sorts' => $queryData['sortParams'],
            'filters' => $queryData['filterData'],
            'adv_params' => $queryData['advParams'],
            'items_count' => $itemsCount,
            'items_ids' => $this->getItemIds($results),
            'selected_ids' => $selectedIds,
            'selectIdsUrl' => $this->getSelectedIdsUrl(),
            'total_results' => $data['total'],
            // 'current_page' => $data['current_page'],
            // 'paginator' => json_encode($paginator),
            'downloadUrl' => $this->getDownloadUrl(),
            'createRoute' => $this->getCreateRoute(),
            'destroyRoute' => $this->getDestroyRoute(),
            'editRoute' => $this->getEditRoute(),
            'route' => 'members.report',
            'showAddButton' => false,
            'selectionEnabled' => $this->getSelectionEnabled(),
            'exportsEnabled' => $this->getExportsEnabled(),
            'advSearchFields' => $this->getSearchFields(),
            'col_headers' => $this->getIndexHeaders(),
            'columns' => $this->getIndexColumns(),
            'title' => $this->getPageTitle(),
            'index_id' => $this->getIndexId(),
        ];
    }

    public function getSearchFields()
    {
        return $this->indexTable
        ->addSearchField(
            key: 'gender',
            displayText: 'Gender',
            valueType: 'list_string',
            options: config('generalSettings.genders'),
            optionsType: 'value_only'
        )
        ->addSearchField(
            key: 'active',
            displayText: 'Status',
            valueType: 'list_string',
            options: [0 => 'Inactive', 1 => 'Active'],
            optionsType: 'key_value'
        )
        ->getAdvSearchFields();
    }

    public function memberReport(
        $searches,
        $indexMode = false,
        $itemsCount = 30,
        $page = 1,
        $download = false,
    ) {
        // $searches = $data['searches'] ?? [];
        if (!$this->authoriseIndex()) {
            throw new AuthorizationException('The user is not authorised to access thi data.');
        }
        $this->preIndexExtra();


        $responseData = [];
        if ($indexMode
            || $searches != null
        ) {
            // if (!$this->sqlOnlyFullGroupBy) {
            //     DB::statement("SET SQL_MODE=''");
            // }
            $queryData = $this->getQueryAndParams(
                $searches,[], []
            );
            if ($download) {
                $results = $queryData['query']->orderBy(
                    $this->orderBy[0],
                    $this->orderBy[1]
                )->select($this->selects)
                ->get();
                return $results;
            } else {
                $results = $queryData['query']->orderBy(
                    $this->orderBy[0],
                    $this->orderBy[1]
                )->paginate(
                    $itemsCount,
                    $this->selects,
                    'page',
                    $page
                );
            }

            // if (!$this->sqlOnlyFullGroupBy) {
            //     DB::statement("SET SQL_MODE='only_full_group_by'");
            // }

            // $this->postIndexExtra();
            $responseData['results'] = $results;
            $responseData['searches'] = $queryData['searchParams'];
        } else {
            $responseData['results'] = null;
        }
        return $responseData;
    }
}

?>
