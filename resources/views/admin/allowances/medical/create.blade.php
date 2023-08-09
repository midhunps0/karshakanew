<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Medical Assistance Application</span>&nbsp;</h3>
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
                    fee_period_from: '',
                    fee_period_to: '',
                    application_date: '',
                    arrear_months: null,
                    bank_name: '',
                    bank_branch: '',
                    account_no: '',
                    ifsc_code: '',
                    history: '',
                    medical_bills: [
                        {
                            no: '',
                            date: '',
                            shop: '',
                            amount: 0
                        }
                    ],
                    bills_total: 0,
                    hospital_name_address: '',
                    patient_mode: '',
                    treatment_period_from: '',
                    treatment_period_to: '',
                    has_availed: null,
                    addBill() {
                        this.medical_bills.push(
                            {
                                no: '',
                                date: '',
                                shop: '',
                                amount: 0
                            }
                        );
                    },
                    removeBill(i) {
                        this.medical_bills = this.medical_bills.filter((b, x) => {
                            return i != x;
                        });
                    },
                    doSubmit() {
                        let el = document.getElementById('med_form');
                        console.log(el);
                        let formData = new FormData(el);
                        axios.post(
                            '{{route('allowances.medical.store')}}',
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
                                    $dispatch('linkaction', {link: '{{route('allowances.medical.show', '_X_')}}'.replace('_X_', r.data.application.id), route: 'allowances.medical.show'})
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
                    $watch('medical_bills', (bills) => {
                        bills_total = bills.reduce((sum, b) => {
                            return sum * 1 + b.amount * 1;
                        }, 0);
                    });
                "
                action=""
                @submit.prevent.stop="
                    doSubmit();
                "
                id="med_form"
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
                <div class="flex flex-row justify-between">
                    <div class="form-control w-1/3">
                        <label class="label opacity-70">
                        <span class="label-text">Application Date</span>
                        </label>
                        <input name="application_date" type="text" placeholder="dd-mm-yyyy" x-model="application_date" class="input input-bordered w-full m ax-w-xs input-sm" pattern="[0-3][0-9]-[0-1][0-9]-[0-2][0-9][0-9][0-9]" required/>
                    </div>
                </div>
                <fieldset class="my-8 p-2 flex flex-row flex-wrap space-x-2 border border-base-content border-opacity-10 rounded-md w-full">
                    <legend>Medical Bills</legend>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Bill No.</th>
                                <th>Date</th>
                                <th>Shop/Lab</th>
                                <th>Amount</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(b, i) in medical_bills">
                                <tr>
                                    <td>
                                        <input :name="'medical_bills['+i+'][no]'" class="input input-sm input-bordered" type="text" x-model="b.no">
                                    </td>
                                    <td>
                                        <input :name="'medical_bills['+i+'][date]'" class="input input-sm input-bordered" type="text" x-model="b.date">
                                    </td>
                                    <td>
                                        <input :name="'medical_bills['+i+'][shop]'" class="input input-sm input-bordered" type="text" x-model="b.shop">
                                    </td>
                                    <td>
                                        <input :name="'medical_bills['+i+'][amount]'" class="input input-sm input-bordered" type="text" x-model="b.amount">
                                    </td>
                                    <td>
                                        <button x-show="medical_bills.length > 1" class="btn btn-xs btn-error"  @click.prevent.stop="removeBill(i);">
                                            <x-easyadmin::display.icon   icon="easyadmin::icons.minus"
                                            height="h-4" width="w-4"/>
                                        </button>
                                        <button x-show="i == medical_bills.length - 1" class="btn btn-xs btn-warning" @click.prevent.stop="addBill();">
                                            <x-easyadmin::display.icon   icon="easyadmin::icons.plus"
                                            height="h-4" width="w-4"/>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr class="rounded-b-md">
                                <td class="bg-base-200" colspan="3" class="text-center">Total</td>
                                <td class="bg-base-200">
                                    <input name="'bills_total" class="input input-sm input-bordered" type="text" x-model="bills_total" readonly>
                                </td>
                                <td class="bg-base-200"></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>

                <fieldset class="my-8 p-2 flex flex-row flex-wrap border border-base-content border-opacity-10 rounded-md w-full">
                    <legend>Treatment Details</legend>
                    <div class="form-control w-1/2 m-0">
                        <label class="label opacity-70">
                        <span class="label-text">Hospital</span>
                        </label>
                        <input name="hospital_name_address" x-model="hospital_name_address" type="text" x-model="hospital_name_address" class="input input-bordered w-full max-w-xs input-sm" />
                    </div>
                    <div class="form-control w-1/2 m-0">
                        <label class="label opacity-70">
                        <span class="label-text">Type of consultation</span>
                        </label>
                        <select name="patient_mode" x-model="patient_mode" class="select select-sm py-0 select-bordered max-w-xs">
                            <option value="Out Patient">Out Patient</option>
                            <option value="In Patient">In Patient</option>
                        </select>
                    </div>
                    <div class="form-control w-1/2">
                        <label class="label opacity-70">
                        <span class="label-text">Treatment Period From</span>
                        </label>
                        <input name="treatment_period_from" type="text" placeholder="dd-mm-yyyy" x-model="treatment_period_from" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" required/>
                    </div>
                    <div class="form-control w-1/2">
                        <label class="label opacity-70">
                        <span class="label-text">Treatment Period To</span>
                        </label>
                        <input name="treatment_period_to" type="text" placeholder="dd-mm-yyyy" x-model="treatment_period_to" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70"  pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                    </div>
                </fieldset>
                <fieldset class="my-8 p-2 flex flex-row flex-wrap border border-base-content border-opacity-10 rounded-md w-full items-end">
                    <legend>Medical assistance data</legend>
                    <div class="form-control w-1/3">
                        <label class="label opacity-70">
                        <span class="label-text">Arrears in Annual Subscription On Delivery Date (No. of months, if any)</span>
                        </label>
                        <input name="arrear_months" type="number" x-model="arrear_months" class="input input-bordered w-full max-w-xs input-sm" required/>
                    </div>
                    <div class="form-control w-1/3 m-0">
                        <label class="label opacity-70">
                        <span class="label-text">Has availed medical assistance earlier?</span>
                        </label>
                        <select name="has_availed" x-model="has_availed" class="select select-sm py-0 select-bordered max-w-xs">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>
                    <div class="form-control w-1/3">
                        <label class="label opacity-70">
                        <span class="label-text">Details of assistance, </span>
                        </label>
                        <textarea class="textarea textarea-xs textarea-bordered" x-model="history" name="history" class="w-full"></textarea>
                    </div>
                    {{-- <div class="form-control w-1/3">
                        <label class="label opacity-70">
                        <span class="label-text">Delivery Count</span>
                        </label>
                        <input name="delivery_count" type="number" x-model="delivery_count" class="input input-bordered w-full max-w-xs input-sm" required/>
                    </div>
                    <div class="form-control w-1/3">
                        <label class="label opacity-70">
                        <span class="label-text">Has availed the maternity allowance earlier? If yes,how many times?</span>
                        </label>
                        <input name="previous_count" type="number" x-model="previous_count" class="input input-bordered w-full max-w-xs input-sm"/>
                    </div> --}}
                </fieldset>
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
                    @if ($member->getSingleMediaUlid('birth_certificate') == null)
                    <div class="w-1/3 p-4">
                        <x-easyadmin::inputs.imageuploader :element="[
                            'key' => 'birth_certificate',
                            'authorised' => true,
                            'label' => 'Birth certificate of the baby',
                            'validations' => [
                                'mime_types' => ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'],
                                'max_size' => '200 kb'
                            ]
                        ]"/>
                    </div>
                    @else
                    <div class="w-1/3 p-4">
                        <input type="hidden" :name="'existing[birth_certificate]'"
                        value="{{$member->getSingleMediaUlid('birth_certificate')}}">
                        <label class="label opacity-70">Birth certificate</label>
                        <img class="w-28" src="{{$member->birth_certificate['path']}}" alt="">
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
