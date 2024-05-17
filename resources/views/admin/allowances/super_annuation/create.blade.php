<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Super Annuation Application</span>&nbsp;</h3>
        <div class="text-right p-4">
            <a href="" class="btn btn-sm" @click.prevent.stop="history.back();" >Back</a>
        </div>
        @if ($member != null)
        <div>
            <form x-data="{
                    member_name: '',
                    member_phone: '',
                    member_aadhaar: '',
                    member_address: '',
                    membership_no: '',
                    member_reg_no: '',
                    member_reg_date: '',
                    member_dob: '',
                    member_age: null,
                    fee_period_from: '',
                    fee_period_to: '',
                    application_date: '',
                    application_no: '',
                    bank_name: '',
                    bank_branch: '',
                    account_no: '',
                    ifsc_code: '',
                    doSubmit() {
                        let el = document.getElementById('sa_form');
                        console.log(el);
                        let formData = new FormData(el);
                        axios.post(
                            '{{route('allowances.super_annuation.store')}}',
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
                                    $dispatch('linkaction', {link: '{{route('allowances.super_annuation.show', '_X_')}}'.replace('_X_', r.data.application.id), route: 'allowances.super_annuation.show'})
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
                    member_name = '{{\App\Helpers\AppHelper::jssafe($member->display_name)}}';
                    member_address = `{{$member->current_address != '' ? $member->current_address : $member->current_address_mal}}`;
                    membership_no = '{{$member->membership_no}}';
                    member_reg_date = '{{$member->reg_date}}';
                    member_phone = '{{$member->mobile_no}}';
                    member_aadhaar = '{{$member->aadhaar_no}}';
                    application_date = '{{$today}}';
                    fee_period_from = '{{$member->lastFeePaidPeriod()['from']}}';
                    fee_period_to = '{{$member->lastFeePaidPeriod()['to']}}';
                "
                action=""
                @submit.prevent.stop="
                    doSubmit();
                "
                id="sa_form"
                >
                <input type="hidden" name="member_id" value="{{$member->id}}">
                <input type="hidden" name="scheme_code" value="{{$scheme_code}}">
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
                        <input name="member_reg_no" type="text" x-model="membership_no" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" readonly/>
                    </div>
                    <div class="form-control w-1/4 max-w-xs">
                        <label class="label opacity-70">
                        <span class="label-text">Members Reg. Date</span>
                        </label>
                        <input name="member_reg_date" type="text" x-model="member_reg_date" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" readonly/>
                    </div>
                    <div class="form-control w-1/4 max-w-xs">
                        <label class="label opacity-70">
                        <span class="label-text">Members Phone</span>
                        </label>
                        <input name="member_phone" type="text" x-model="member_phone" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70"/>
                    </div>
                </div>
                <div class="flex flex-row space-x-2">
                    <div class="form-control w-1/4 max-w-xs">
                        <label class="label opacity-70">
                        <span class="label-text">Member's Date Of Birth</span>
                        </label>
                        <input name="member_dob" type="text" x-model="member_dob" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" required/>
                    </div>
                    <div class="form-control w-1/4 max-w-xs">
                        <label class="label opacity-70">
                        <span class="label-text">Member's Age</span>
                        </label>
                        <input name="member_age" type="text" x-model="member_age" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" required/>
                    </div>
                </div>
                <div class="flex flex-row space-x-2">
                    <div class="form-control w-1/3">
                        <label class="label opacity-70">
                        <span class="label-text">Member Address</span>
                        </label>
                        <textarea name="member_address" x-model="member_address" class="textarea textarea-sm textarea-bordered h-24 max-w-xs"></textarea>
                    </div>
                    <fieldset class="flex flex-row my-2 p-4 space-x-2 border border-base-content border-opacity-10 rounded-md w-2/3">
                        <legend class="font-bold">Last Fee Paid Period</legend>
                        <div class="form-control w-full max-w-xs">
                            <label class="label opacity-70">
                            <span class="label-text">From</span>
                            </label>
                            <input name="fee_period_from" type="text" placeholder="dd-mm-yyyy" x-model="fee_period_from" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" required/>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label opacity-70">
                            <span class="label-text">To</span>
                            </label>
                            <input name="fee_period_to" type="text" placeholder="dd-mm-yyyy" x-model="fee_period_to" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70"  pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                        </div>
                    </fieldset>
                    <div class="form-control w-1/4 max-w-xs">
                        <label class="label opacity-70">
                        <span class="label-text">Aadhaar Number</span>
                        </label>
                        <input name="member_aadhaar" type="text" x-model="member_aadhaar" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70"/>
                    </div>
                </div>
                <hr class="border border-base-content border-opacity-20 my-4">
                <div class="flex flex-row justify-start gap-4">
                    <div class="form-control w-1/3">
                        <label class="label opacity-70">
                        <span class="label-text">Application Date</span>
                        </label>
                        <input name="application_date" type="text" placeholder="dd-mm-yyyy" x-model="application_date" class="input input-bordered w-full max-w-xs input-sm" pattern="[0-3][0-9]-[0-1][0-9]-[0-2][0-9][0-9][0-9]" placeholder="dd-mm-yyyy" required/>
                    </div>
                    <div x-data="{
                            isOld: false,
                        }"
                        class="flex-grow flex flex-row justify-evenly items-center bg-base-200 p-2 rounded-md">
                        <div>
                            Is old?
                            <input x-model="isOld" type="checkbox" class="checkbox checkbox-primary">
                        </div>
                        <div class="form-control w-1/3">
                            <label class="label opacity-70">
                            <span class="label-text">Application No.</span>
                            </label>
                            <input name="application_no" type="text" x-model="application_no" class="input input-bordered w-full m ax-w-xs input-sm read-only:bg-base-200" :required="isOld" :readonly="!isOld"/>
                        </div>
                    </div>
                </div>

                <fieldset class="my-8 p-2 flex flex-row space-x-2 border border-base-content border-opacity-10 rounded-md w-full">
                    <legend>Bank Details</legend>
                    <div class="form-control w-1/4">
                        <label class="label opacity-70">
                        <span class="label-text">Name Of Payee</span>
                        </label>
                        <input name="member_bank_account[bank_name]" type="text" x-model="bank_name" class="input input-bordered w-full max-w-xs input-sm" />
                    </div>
                    <div class="form-control w-1/4">
                        <label class="label opacity-70">
                        <span class="label-text">Bank & Branch</span>
                        </label>
                        <input name="member_bank_account[bank_branch]" type="text" x-model="bank_branch" class="input input-bordered w-full max-w-xs input-sm" />
                    </div>
                    <div class="form-control w-1/4">
                        <label class="label opacity-70">
                        <span class="label-text">Account No.</span>
                        </label>
                        <input name="member_bank_account[account_no]" type="text" x-model="account_no" class="input input-bordered w-full max-w-xs input-sm" />
                    </div>
                    <div class="form-control w-1/4">
                        <label class="label opacity-70">
                        <span class="label-text">IFSC</span>
                        </label>
                        <input name="member_bank_account[ifsc_code]" type="text" x-model="ifsc_code" class="input input-bordered w-full max-w-xs input-sm" />
                    </div>
                </fieldset>
                <div class="flex flex-row flex-wrap">
                    @if ($member->getSingleMediaUlid('wb_passbook_front') == null)
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
                        value="{{$member->getSingleMediaUlid('wb_passbook_front')}}">
                        <label class="label opacity-70">Welfare Board Passbook (Front)</label>
                        <img class="w-28" src="{{$member->wb_passbook_front['path']}}" alt="">
                    </div>
                    @endif
                    @if ($member->getSingleMediaUlid('wb_passbook_back') == null)
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
                        value="{{$member->getSingleMediaUlid('wb_passbook_back')}}">
                        <label class="label opacity-70">Welfare Board Passbook (Back)</label>
                        <img class="w-28" src="{{$member->wb_passbook_back['path']}}" alt="">
                    </div>
                    @endif
                    @if ($member->getSingleMediaUlid('aadhaar_card') == null)
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
                        value="{{$member->getSingleMediaUlid('aadhaar_card')}}">
                        <label class="label opacity-70">Aadhaar Card</label>
                        <img class="w-28" src="{{$member->aadhaar_card['path']}}" alt="">
                    </div>
                    @endif
                    @if ($member->getSingleMediaUlid('bank_passbook') == null)
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
                        value="{{$member->getSingleMediaUlid('bank_passbook')}}">
                        <label class="label opacity-70">Bank Passbook Front Page</label>
                        <img class="w-28" src="{{$member->bank_passbook['path']}}" alt="">
                    </div>
                    @endif
                    @if ($member->getSingleMediaUlid('union_certificate') == null)
                    <div class="w-1/3 p-4">
                        <x-easyadmin::inputs.imageuploader :element="[
                            'key' => 'union_certificate',
                            'authorised' => true,
                            'label' => 'Union Certificate',
                            'validations' => [
                                'mime_types' => ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'],
                                'max_size' => '200 kb'
                            ]
                        ]"/>
                    </div>
                    @else
                    <div class="w-1/3 p-4">
                        <input type="hidden" :name="'existing[union_certificate]'"
                        value="{{$member->getSingleMediaUlid('union_certificate')}}">
                        <label class="label opacity-70">Union Certificate</label>
                        <img class="w-28" src="{{$member->ration_card['path']}}" alt="">
                    </div>
                    @endif
                    @if ($member->getSingleMediaUlid('ration_card') == null)
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
                    @else
                    <div class="w-1/3 p-4">
                        <input type="hidden" :name="'existing[ration_card]'"
                        value="{{$member->getSingleMediaUlid('ration_card')}}">
                        <label class="label opacity-70">Ration Card</label>
                        <img class="w-28" src="{{$member->ration_card['path']}}" alt="">
                    </div>
                    @endif
                    @if ($member->getSingleMediaUlid('one_and_same_certificate') == null)
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
                    @else
                    <div class="w-1/3 p-4">
                        <input type="hidden" :name="'existing[one_and_same_certificate]'"
                        value="{{$member->getSingleMediaUlid('one_and_same_certificate')}}">
                        <label class="label opacity-70">One and same certificate</label>
                        <img class="w-28" src="{{$member->ration_card['path']}}" alt="">
                    </div>
                    @endif
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-sm btn-primary min-w-24">Submit</button>
                </div>
            </form>
        </div>
        @else
        <div class="text-error text-center font-bold">
            You are not authorised to create allowances for this member.
        </div>
        @endif
    </div>
</x-easyadmin::partials.adminpanel>
