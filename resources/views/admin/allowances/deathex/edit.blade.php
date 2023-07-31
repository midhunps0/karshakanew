<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Death Ex-Gracia Application</span>&nbsp;</h3>
        <div class="text-right p-4">
            <a href="" class="btn btn-sm" @click.prevent.stop="history.back();" >Back</a>
        </div>
        <div>
            <form x-data="{
                    member_name: '',
                    member_address: '',
                    membership_no: '',
                    application_date: '',
                    arrears_months: null,
                    applicant_name: '',
                    applicant_address: '',
                    applicant_phone: '',
                    applicant_aadhaar: '',
                    applicant_is_minor: '',
                    applicant_relation: '',
                    bank_name: '',
                    bank_branch: '',
                    account_no: '',
                    ifsc_code: '',
                    date_of_death: '',
                    marital_status: '',
                    doSubmit() {
                        let el = document.getElementById('dex_form');
                        console.log(el);
                        let formData = new FormData(el);
                        axios.post(
                            '{{route('allowances.postdeath.store')}}',
                            formData,
                            {
                                headers: {
                                    'Content-Type': 'multipart/form-data',
                                },
                            }
                        ).then((r) => {
                            console.log(r);
                            if (r.data.success) {
                                $dispatch('showtoast', {message: 'Application Created.', mode: 'success', });
                                setTimeout(() => {
                                    $dispatch('linkaction', {link: '{{route('allowances.postdeath.show', '_X_')}}'.replace('_X_', r.data.application.id), route: 'allowances.postdeath.show'})
                                }, 500);
                            } else {
                                $dispatch('shownotice', {message: r.data.message, mode: 'error', redirectUrl: null, redirectRoute: null});
                            }
                        }).catch((e) => {
                            console.log(e);
                        });
                    }
                }"
                x-init="
                    member_name = '{{\App\Helpers\AppHelper::jssafe($allowance->member->display_name)}}';
                    member_address = `{{$allowance->member->current_address != '' ? $allowance->member->current_address : $allowance->member->current_address_mal}}`;
                    membership_no = '{{$allowance->member->membership_no}}';
                    application_date = '{{$today}}';
                "
                action=""
                @submit.prevent.stop="
                    doSubmit();
                "
                id="dex_form"
                >
                <input type="hidden" name="member_id" value="{{$allowance->member->id}}">
                <input type="hidden" name="scheme_code" value="{{$allowance->welfarescheme->code}}">
                <div class="flex flex-row space-x-2">
                    <div class="form-control w-1/4 max-w-xs">
                        <label class="label opacity-70">
                        <span class="label-text">Member's Name</span>
                        </label>
                        <input name="member_name" type="text" x-model="member_name" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" readonly/>
                    </div>
                    <div class="form-control w-1/4 max-w-xs">
                        <label class="label opacity-70">
                        <span class="label-text">Membership No.</span>
                        </label>
                        <input name="membership_no" type="text" x-model="membership_no" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" readonly/>
                    </div>
                    <div class="form-control w-1/4 max-w-xs">
                        <label class="label opacity-70">
                        <span class="label-text">Date of death</span>
                        </label>
                        <input name="date_of_death" type="text" x-model="date_of_death" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70"/>
                    </div>
                    <div class="form-control w-1/4 max-w-xs">
                        <label class="label opacity-70">
                        <span class="label-text">Marital Status</span>
                        </label>
                        <input name="marital_status" type="text" x-model="marital_status" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" />
                    </div>
                </div>
                <div class="flex flex-row space-x-2">
                    <div class="form-control w-1/3">
                        <label class="label opacity-70">
                        <span class="label-text">Member Address</span>
                        </label>
                        <textarea name="member_address" x-model="member_address" class="textarea textarea-sm textarea-bordered h-24 max-w-xs"></textarea>
                    </div>
                </div>
                <hr class="border border-base-content border-opacity-20 my-4">
                <div class="flex flex-row justify-between">
                    <div class="form-control w-1/3">
                        <label class="label opacity-70">
                        <span class="label-text">Application Date</span>
                        </label>
                        <input name="application_date" type="text" placeholder="dd-mm-yyyy" x-model="application_date" class="input input-bordered w-full m ax-w-xs input-sm" pattern="[0-3][0-9]-[0-1][0-9]-[0-2][0-9][0-9][0-9]" required/>
                    </div>
                </div>
                <fieldset class="my-8 p-2 flex flex-row flex-wrap space-x-2 border border-base-content border-opacity-10 rounded-md w-full">
                    <legend>Applicant Details</legend>
                    <div class="form-control w-1/4 max-w-xs">
                        <label class="label opacity-70">
                        <span class="label-text">Name</span>
                        </label>
                        <input name="applicant_name" type="text" x-model="applicant_name" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70"/>
                    </div>
                    <div class="form-control w-1/4 max-w-xs">
                        <label class="label opacity-70">
                        <span class="label-text">Address</span>
                        </label>
                        <textarea name="applicant_address" x-model="applicant_address" class="textarea textarea-sm textarea-bordered h-24 max-w-xs"></textarea>
                    </div>
                    <div class="form-control w-1/4">
                        <label class="label opacity-70">
                        <span class="label-text">Mobile No.</span>
                        </label>
                        <input name="applicant_phone" type="text" x-model="applicant_mobile_no" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" required/>
                    </div>
                    <div class="form-control w-1/4">
                        <label class="label opacity-70">
                        <span class="label-text">Aadhaar No.</span>
                        </label>
                        <input name="applicant_aadhaar" type="text" x-model="applicant_aadhaar" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" required/>
                    </div>
                    <div class="form-control w-1/4">
                        <label class="label opacity-70">
                        <span class="label-text">Relation to the member</span>
                        </label>
                        <input name="applicant_relation" type="text" x-model="applicant_relation" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" required/>
                    </div>
                    <div class="form-control w-xs">
                        <label class="label opacity-70">
                          <span class="label-text">Is the applicant minor?</span>
                        </label>
                        <select name="applicant_is_minor" x-model="applicant_is_minor" class="select select-bordered">
                          <option value="0">No</option>
                          <option value="1">Yes</option>
                        </select>
                      </div>
                </fieldset>
                <fieldset class="my-8 p-2 flex flex-row space-x-2 border border-base-content border-opacity-10 rounded-md w-full">
                    <legend>Bank Details</legend>
                    <div class="form-control w-1/4">
                        <label class="label opacity-70">
                        <span class="label-text">Name Of Payee</span>
                        </label>
                        <input name="applicant_bank_details[bank_name]" type="text" x-model="bank_name" class="input input-bordered w-full max-w-xs input-sm" />
                    </div>
                    <div class="form-control w-1/4">
                        <label class="label opacity-70">
                        <span class="label-text">Bank & Branch</span>
                        </label>
                        <input name="applicant_bank_details[bank_branch]" type="text" x-model="bank_branch" class="input input-bordered w-full max-w-xs input-sm" />
                    </div>
                    <div class="form-control w-1/4">
                        <label class="label opacity-70">
                        <span class="label-text">Account No.</span>
                        </label>
                        <input name="applicant_bank_details[account_no]" type="text" x-model="account_no" class="input input-bordered w-full max-w-xs input-sm" />
                    </div>
                    <div class="form-control w-1/4">
                        <label class="label opacity-70">
                        <span class="label-text">IFSC</span>
                        </label>
                        <input name="applicant_bank_details[ifsc_code]" type="text" x-model="ifsc_code" class="input input-bordered w-full max-w-xs input-sm" />
                    </div>
                </fieldset>
                <div class="flex flex-row flex-wrap">
                    @if ($allowance->member->getSingleMediaUlid('wb_passbook_front') == null)
                    <div class="w-1/3 p-4">
                        <x-easyadmin::inputs.imageuploader :element="[
                            'key' => 'wb_passbook_front',
                            'authorised' => true,
                            'label' => 'Welfare Board Passbook (Front)',
                            'validations' => [
                                'mime_types' => ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'],
                                'max_size' => '200 kb'
                            ]
                        ]"/>
                    </div>
                    @else
                    <div class="w-1/3 p-4">
                        <input type="hidden" :name="'existing[wb_passbook_front]'"
                        value="{{$allowance->member->getSingleMediaUlid('wb_passbook_front')}}">
                        <label class="label opacity-70">Welfare Board Passbook (Front)</label>
                        <img class="w-28" src="{{$allowance->member->wb_passbook_front['path']}}" alt="">
                    </div>
                    @endif
                    @if ($allowance->member->getSingleMediaUlid('wb_passbook_back') == null)
                    <div class="w-1/3 p-4">
                        <x-easyadmin::inputs.imageuploader :element="[
                            'key' => 'wb_passbook_back',
                            'authorised' => true,
                            'label' => 'Welfare Board Passbook (Back)',
                            'validations' => [
                                'mime_types' => ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'],
                                'max_size' => '200 kb'
                            ]
                        ]"/>
                    </div>
                    @else
                    <div class="w-1/3 p-4">
                        <input type="hidden" :name="'existing[wb_passbook_back]'"
                        value="{{$allowance->member->getSingleMediaUlid('wb_passbook_back')}}">
                        <label class="label opacity-70">Welfare Board Passbook (Back)</label>
                        <img class="w-28" src="{{$allowance->member->wb_passbook_back['path']}}" alt="">
                    </div>
                    @endif
                    @if ($allowance->member->getSingleMediaUlid('aadhaar_card') == null)
                    <div class="w-1/3 p-4">
                        <x-easyadmin::inputs.imageuploader :element="[
                            'key' => 'aadhaar_card',
                            'authorised' => true,
                            'label' => 'Aadhaar Card',
                            'validations' => [
                                'mime_types' => ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'],
                                'max_size' => '200 kb'
                            ]
                        ]"/>
                    </div>
                    @else
                    <div class="w-1/3 p-4">
                        <input type="hidden" :name="'existing[aadhaar_card]'"
                        value="{{$allowance->member->getSingleMediaUlid('aadhaar_card')}}">
                        <label class="label opacity-70">Aadhaar Card</label>
                        <img class="w-28" src="{{$allowance->member->aadhaar_card['path']}}" alt="">
                    </div>
                    @endif
                    @if ($allowance->member->getSingleMediaUlid('bank_passbook') == null)
                    <div class="w-1/3 p-4">
                        <x-easyadmin::inputs.imageuploader :element="[
                            'key' => 'bank_passbook',
                            'authorised' => true,
                            'label' => 'Bank Passbook Front Page',
                            'validations' => [
                                'mime_types' => ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'],
                                'max_size' => '200 kb'
                            ]
                        ]"/>
                    </div>
                    @else
                    <div class="w-1/3 p-4">
                        <input type="hidden" :name="'existing[bank_passbook]'"
                        value="{{$allowance->member->getSingleMediaUlid('bank_passbook')}}">
                        <label class="label opacity-70">Bank Passbook Front Page</label>
                        <img class="w-28" src="{{$allowance->member->bank_passbook['path']}}" alt="">
                    </div>
                    @endif
                    <div class="w-1/3 p-4">
                        <x-easyadmin::inputs.imageuploader :element="[
                            'key' => 'ration_card',
                            'authorised' => true,
                            'label' => 'Ration Card',
                            'validations' => [
                                'mime_types' => ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'],
                                'max_size' => '200 kb'
                            ]
                        ]"/>
                    </div>
                    <div class="w-1/3 p-4">
                        <x-easyadmin::inputs.imageuploader :element="[
                            'key' => 'one_and_same_certificate',
                            'authorised' => true,
                            'label' => 'One and same certificate',
                            'validations' => [
                                'mime_types' => ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'],
                                'max_size' => '200 kb'
                            ]
                        ]"/>
                    </div>
                    <div class="w-1/3 p-4">
                        <x-easyadmin::inputs.imageuploader :element="[
                            'key' => 'death_certificate',
                            'authorised' => true,
                            'label' => 'Death certificate',
                            'validations' => [
                                'mime_types' => ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'],
                                'max_size' => '200 kb'
                            ]
                        ]"/>
                    </div>
                    <div class="w-1/3 p-4">
                        <x-easyadmin::inputs.imageuploader :element="[
                            'key' => 'minor_age_proof',
                            'authorised' => true,
                            'label' => 'Age Proof (if minor)',
                            'validations' => [
                                'mime_types' => ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'],
                                'max_size' => '200 kb'
                            ]
                        ]"/>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-sm btn-primary min-w-24">Submit</button>
                </div>
            </form>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>
