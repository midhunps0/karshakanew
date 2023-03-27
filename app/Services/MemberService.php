<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Taluk;
use App\Models\Member;
use App\Models\Village;
use App\Models\District;
use App\Helpers\AppHelper;
use App\Models\FeeCollection;
use App\Models\FeeItem;
use App\Models\TradeUnion;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
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
use Ynotz\MediaManager\Services\EAInputMediaValidator;

class MemberService implements ModelViewConnector {
    use IsModelViewConnector;

    private $indexTable;

    protected $mediaFields = [
        'aadhaar_card',
        'bank_passbook'
    ];


    public function __construct()
    {
        $this->modelClass = '\\App\\Models\\Member';
        $this->indexTable = new IndexTable();
        $this->searchesMap = [
            'taluk' => 'taluk_id',
            'district' => 'district_id'
        ];
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
            sort: ['key' => 'name'],
        )->addHeaderColumn(
            title: 'Membership No.',
            // search: [
            //     'key' => 'membership_no',
            //     'condition' => 'st',
            //     'label' => 'Search Members'
            // ],
        )->addHeaderColumn(
            title: 'Aadhaar No.',
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
            title: 'Actions'
        );
        return $columns->getHeaderRow();
    }

    protected function getIndexColumns(): array
    {
        $columns = $this->indexTable->addColumn(
            fields: ['name']
        )->addColumn(
            fields: ['membership_no']
        )->addColumn(
            fields: ['aadhaar_no']
        );
        $user = User::find(auth()->user()->id);
        if ($user->hasPermissionTo('Member: View In Any District')) {
            $columns = $columns->addColumn(
                fields: ['name'],
                relation: 'district'
            );
        }
        $columns = $columns->addColumn(
            fields: ['name'],
            relation: 'taluk'
        )->addActionColumn(
            editRoute: $this->getEditRoute(),
            deleteRoute: $this->getDestroyRoute()
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

    public function buildSearchFormLayout(Type $args)
    {
        # code...
    }

    public function getCreatePageData(): array
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
                success_redirect_route: 'members.index',
                items: $this->getCreateFormElements(),
                layout: $this->buildCreateFormLayout(),
                label_position: 'top',
                width: 'full',
                type: 'easyadmin::partials.simpleform'
            ),
        ];
    }

    public function getEditPageData($id): array
    {
        $member = ($this->modelClass)::where('id', $id)->get()->first();
        return [
            'title' => 'Members',
            '_old' => $member,
            'form' => FormHelper::makeEditForm(
                title: 'Edit Member',
                id: 'form_members_edit',
                action_route: 'members.update',
                action_route_params: ['id' => $id],
                success_redirect_route: 'members.index',
                items: $this->getEditFormElements($member),
                layout: $this->buildCreateFormLayout(),
                label_position: 'top',
                width: 'full',
                type: 'easyadmin::partials.simpleform'
            )
        ];
    }

    public function getStoreValidationRules(): array
    {
        return [
            'name' => ['required',],
            'name_mal' => ['required',],
            'dob' => ['required',],
            'gender' => ['required',],
            'marital_status' => ['required',],
            'mobile_no' => ['required',],
            'aadhaar_no' => ['required',],
            'parent_guardian' => ['required',],
            'guardian_relationship' => ['required',],
            'current_address' => ['required',],
            'current_address_mal' => ['required',],
            'ca_pincode' => ['required',],
            'copy_address' => ['sometimes'],
            'permanent_address' => ['required_if:copy_address,false',],
            'permanent_address_mal' => ['required_if:copy_address,false',],
            'pa_pincode' => ['required_if:copy_address,false',],
            'district' => ['sometimes',],
            'taluk' => ['required',],
            'village' => ['required',],
            'tradeUnion' => ['required',],
            'bank_acc_no' => ['required',],
            'bank_name' => ['required',],
            'bank_branch' => ['required',],
            'bank_ifsc' => ['required',],
            'aadhaar_card' => (new EAInputMediaValidator())
                ->maxSize(200, 'kb')
                ->mimeTypes(['jpeg', 'jpg', 'png'])
                ->getRules(),
            'bank_passbook' => (new EAInputMediaValidator())
                ->maxSize(200, 'kb')
                ->mimeTypes(['jpeg', 'jpg', 'png'])
                ->getRules()
        ];
    }

    public function getUpdateValidationRules($id): array
    {
        $rules = $this->getStoreValidationRules();
        return $rules;
    }

    private function formElements(Member $member = null): array
    {
        $talukoptions = isset($member) ? Taluk::inDistrict($member->district_office_id)->get() : [];
        $villageoptions = isset($member) ? Village::inTaluk($member->taluk_id)->get() : [];
        return [
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
                properties: ['required' => true],
            ),
            'gender' => FormHelper::makeSelect(
                key: 'gender',
                label: 'Gender',
                options: config('generalSettings.genders'),
                options_type: 'value_only',
                properties: ['required' => true],
            ),
            'dob' => FormHelper::makeDatePicker(
                key: 'dob',
                label: 'Date of Birth',
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
            'aadhaar_no' => FormHelper::makeInput(
                inputType: 'text',
                key: 'aadhaar_no',
                label: 'Aadhaar No.',
                properties: ['required' => true],
            ),
            'election_card_no' => FormHelper::makeInput(
                inputType: 'text',
                key: 'election_card_no',
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
            'guardian_relationship' => FormHelper::makeInput(
                inputType: 'text',
                key: 'guardian_relationship',
                label: 'Relationship',
                properties: ['required' => true],
            ),
            'current_address' => FormHelper::makeTextarea(
                key: 'current_address',
                label: 'Current Address',
                properties: ['required' => true],
            ),
            'current_address_mal' => FormHelper::makeTextarea(
                key: 'current_address_mal',
                label: 'Current Address In Malayalam',
                properties: ['required' => true],
            ),
            'ca_pincode' => FormHelper::makeInput(
                inputType: 'text',
                key: 'ca_pincode',
                label: 'Current Addr. PIN Code',
                properties: ['required' => true],
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
                label: 'Same as current address',
                fireInputEvent: true
            ),
            'permanent_address' => FormHelper::makeTextarea(
                key: 'permanent_address',
                label: 'Permanent Address',
                toggleOnEvents: ['copy_address' => [['==', true, false], ['==', false, true]]],
                properties: ['required' => true],
            ),
            'permanent_address_mal' => FormHelper::makeTextarea(
                key: 'permanent_address_mal',
                label: 'Permanent Address In Malayalam',
                toggleOnEvents: ['copy_address' => [['==', true, false], ['==', false, true]]],
                properties: ['required' => true],
            ),
            'pa_pincode' => FormHelper::makeInput(
                inputType: 'text',
                key: 'pa_pincode',
                label: 'Permanent Addr. PIN Code',
                toggleOnEvents: ['copy_address' => [['==', true, false], ['==', false, true]]],
                properties: ['required' => true],
            ),
            'district' => FormHelper::makeSelect(
                key: 'district',
                label: 'District',
                options: District::all()->pluck('name', 'id'),
                options_type: 'key_value',
                properties: ['required' => true],
            ),
            'districtOffice' => FormHelper::makeSelect(
                key: 'districtOffice',
                label: 'District Office',
                options: District::all()->pluck('name', 'id'),
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
                label: 'Bank Name',
                properties: ['required' => true],
            ),
            'bank_branch' => FormHelper::makeInput(
                inputType: 'text',
                key: 'bank_branch',
                label: 'Bank Branch',
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
            'aadhaar_card' => FormHelper::makeImageUploader(
                key: 'aadhaar_card',
                label: 'Aadhaar card',
                properties: ['required' => true, 'multiple' => false],
                theme: 'regular',
                allowGallery: false,
                validations: [
                    'max_size' => '200 kb',
                    'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                    ]
                // fireInputEvent: true
            ),
            'bank_passbook' => FormHelper::makeImageUploader(
                key: 'bank_passbook',
                label: 'Bank Passbook',
                properties: ['required' => true, 'multiple' => false],
                theme: 'regular',
                allowGallery: false,
                validations: [
                    'max_size' => '200 kb',
                    'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                    ]
                // fireInputEvent: true
            ),
        ];
    }

    public function authoriseCreate()
    {
        $u = User::find(auth()->user()->id);
        return $u->hasRole('System Admin') || $u->hasPermissionTo('Member: Create');
    }

    private function getQuery()
    {
        return $this->modelClass::query()
            ->userAccessControlled()
            ->with(
                [
                    'taluk' => function ($query) {
                        $query->select('id', 'name');
                    }
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
            ]
        ];
    }

    public function buildCreateFormLayout(): array
    {
        $layout = (new ColumnLayout())
            ->addElements(
                [
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
                            ))->addInputSlot('aadhaar_no'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('parent_guardian'),
                            (new ColumnLayout(
                                width: '1/4'
                            ))->addInputSlot('guardian_relationship')
                        ]
                    ),
                    (new RowLayout(width: 'full'))
                        ->addElement(new SectionDivider('Current Address')),
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
                        ->addElement(new SectionDivider('Permanent Address')),
                    (new RowLayout(width: 'full'))->addInputSlot('copy_address'),
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
                        ->addElement(new SectionDivider('Image Uploads')),
                    (new RowLayout())->addElements(
                        [
                            (new ColumnLayout(
                                width: '1/2'
                            ))->addInputSlot('aadhaar_card'),
                            (new ColumnLayout(
                                width: '1/2'
                            ))->addInputSlot('bank_passbook'),
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
    //                         ))->addInputSlot('election_card_no'),
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
        if (filter_var($data['copy_address'], FILTER_VALIDATE_BOOLEAN)) {
            $data['permanent_address'] = $data['current_address'];
            $data['permanent_address_mal'] = $data['current_address_mal'];
            $data['pa_pincode'] = $data['ca_pincode'];
        }
        unset($data['copy_address']);

        $data['created_by'] = auth()->user()->id;
        $data['dob'] = AppHelper::formatDateForSave($data['dob']);
        return $data;
    }

    public function processBeforeUpdate(array $data): array
    {
        if (filter_var($data['copy_address'], FILTER_VALIDATE_BOOLEAN)) {
            $data['permanent_address'] = $data['current_address'];
            $data['permanent_address_mal'] = $data['current_address_mal'];
            $data['pa_pincode'] = $data['ca_pincode'];
        }
        unset($data['copy_address']);

        $data['created_by'] = auth()->user()->id;
        $data['dob'] = AppHelper::formatDateForSave($data['dob']);
        return $data;
    }

    public function suggestionslist($data)
    {
        $members = Member::userAccessControlled()
            ->with('taluk')
            ->where('membership_no', 'like', $data['membership_no'].'%')
            ->get();
        return $members;
    }

    public function fetch($id)
    {
        return Member::userAccessControlled()
            ->with('feePayments')
            ->where('id', $id)
            ->get()->first();
    }

    public function annualFeesPeriod($id, $months): array
    {
        // $m = Member::find($id);
        // $fc = $m->feeItems()->where('fee_type_id', 1)->get();
        $fi = DB::table('members as m')->join(
            'fee_collections as fc', 'm.id', '=', 'fc.member_id'
        )->join(
            'fee_items as fi', 'fi.fee_collection_id', '=', 'fc.id'
        )->select('m.id', 'fc.id', 'fc.receipt_date', 'fi.*')
            ->where('m.id', $id)
            ->orderBy('fc.receipt_date', 'desc')
            ->get()->first();
        // dd($fc->period_to);
        if ($fi != null) {
            $lastPaid = Carbon::parse($fi->period_to);
            $from = $lastPaid;
            $to = $lastPaid;
            $from = $lastPaid->addDay()->startOfMonth()
                ->subMonthsNoOverflow()->format('d-m-Y');
            $to = $lastPaid->subDay()->addMonths($months)
                ->subMonthsNoOverflow()->endOfMonth()->format('d-m-Y');
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
        $member = Member::find($id);
        $distict = $member->district;
        if ($member == null) {
            return false;
        }
        try {
            DB::beginTransaction();
            $bookNo = $data['book_no'] ?? AppHelper::getBookNumber($distict);
            $receiptNo = $data['receipt_no'] ?? AppHelper::getReceiptNumber($distict);
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
                    $fiData['period_from'] = AppHelper::formatDateForSave($item['period_from']);
                }
                if (isset($item['period_to'])) {
                    $fiData['period_to'] = AppHelper::formatDateForSave($item['period_to']);
                }
                if (isset($item['period_from']) && isset($item['period_to'])) {
                    $fiData['tenure'] = $item['period_from'] . ' to ' . $item['period_to'];
                }
                FeeItem::create($fiData);
            }
            $fc->refresh();
            $fc->total_amount = $sum;
            $fc->save();
            DB::commit();
            info('Receipt Created.');
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
            return false;
        }

    }
}

?>