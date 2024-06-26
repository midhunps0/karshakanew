<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Higher Education Allowance Application</span>&nbsp;</h3>
        <div class="text-right p-4">
            <a href="" class="btn btn-sm" @click.prevent.stop="history.back();" >Back</a>
        </div>
        <div>
            <form x-data="{
                    member_name: '',
                    member_address: '',
                    membership_no: '',
                    fee_period_from: '',
                    fee_period_to: '',
                    student_name: '',
                    application_date: '',
                    application_no: '',
                    arrears_months: null,
                    mobile_no: '',
                    aadhaar_no: '',
                    bank_name: '',
                    bank_branch: '',
                    account_no: '',
                    ifsc_code: '',
                    is_sc_st: false,
                    passed_exam_details: {
                        exam_name: '',
                        reg_no: '',
                        institution: '',
                        affiliated_board: '',
                        exam_start_date: '',
                    },
                    marks: [
                        {
                            subject: '',
                            marks_scored: '',
                            max_mark: '',
                            percentage: '',
                            grade: '',
                            points: '',
                        }
                    ],
                    marks_total: {
                        total_scored: '',
                        total_max: '',
                        percentage: '',
                        points: '',
                    },
                    addSubject() {
                        this.marks.push(
                            {
                                subject: '',
                                marks_scored: '',
                                max_mark: '',
                                points: '',
                                percentage: ''
                            }
                        );
                    },
                    removeSubject(i) {
                        this.marks = this.marks.filter((m, index) => {
                            return i != index;
                        });
                    },
                    setPercentage(item) {
                        if(item.max_mark == 0 || item.max_mark == '') {
                            item.percentage = '';
                        } else {
                            item.percentage = Math.round(item.marks_scored / item.max_mark * 10000) / 100 ;
                        }
                    },
                    setPoints(item) {
                        switch(item.grade) {
                            case 'A+':
                                item.points = 9;
                                break;
                            case 'A':
                                item.points = 8;
                                break;
                            case 'B+':
                                item.points = 7;
                                break;
                            case 'B':
                                item.points = 6;
                                break;
                            case 'C+':
                                item.points = 5;
                                break;
                            case 'C':
                                item.points = 4;
                                break;
                            case 'D+':
                                item.points = 3;
                                break;
                        }
                    },
                    doSubmit() {
                        let el = document.getElementById('hedu_form');
                        console.log(el);
                        let formData = new FormData(el);
                        axios.post(
                            '{{route('allowances.higher_education.store')}}',
                            formData,
                            {
                                headers: {
                                    'Content-Type': 'multipart/form-data',
                                },
                                {{-- onUploadProgress: (event) => {
                                    let pc = Math.floor((event.loaded * 100) / event.total);
                                    this.files.forEach((f) => {
                                        if (f.id == file.id) { f.uploaded_pc = pc; }
                                    });
                                }, --}}
                            }
                        ).then((r) => {
                            console.log(r);
                            if (r.data.success) {
                                $dispatch('showtoast', {message: 'Application Created.', mode: 'success', });
                                setTimeout(() => {
                                    $dispatch('linkaction', {link: '{{route('allowances.higher_education.show', '_X_')}}'.replace('_X_', r.data.application.id), route: 'allowances.higher_education.show'})
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
                    fee_period_from = '{{$member->lastFeePaidPeriod()['from']}}';
                    fee_period_to = '{{$member->lastFeePaidPeriod()['to']}}';
                    mobile_no = '{{$member->mobile_no}}';
                    aadhaar_no = '{{$member->aadhaar_no}}';
                    application_date = '{{$today}}';
                    $watch('marks', (marks) => {
                        let pointsSum = 0;
                        let tscored = 0;
                        let tmax = 0;
                        let pc = 0;
                        marks.forEach((m) => {
                            pointsSum += m.points * 1;
                        });
                        marks_total.points = pointsSum;
                        marks.forEach((m) => {
                            tscored += m.marks_scored * 1;
                            tmax += m.max_mark * 1;
                        });
                        pc = tmax > 0 ? Math.round(tscored / tmax * 10000) / 100 : '';
                        marks_total.total_scored = tscored * 1;
                        marks_total.total_max = tmax * 1;
                        marks_total.percentage = pc * 1.0;
                        marks_total.points = pointsSum;
                    });
                "
                action=""
                @submit.prevent.stop="
                    doSubmit();
                "
                id="hedu_form"
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
                        <input name="membership_no" type="text" x-model="membership_no" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" readonly/>
                    </div>
                    <div class="form-control w-1/4">
                        <label class="label opacity-70">
                        <span class="label-text">Member's Phone No.</span>
                        </label>
                        <input name="member_phone" type="text" x-model="mobile_no" class="input input-bordered w-full max-w-xs input-sm" />
                    </div>
                    <div class="form-control w-1/4">
                        <label class="label opacity-70">
                        <span class="label-text">Member's Aadhaar No.</span>
                        </label>
                        <input name="member_aadhaar" type="text" x-model="aadhaar_no" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" required/>
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
                </div>
                <hr class="border border-base-content border-opacity-20 my-4">
                <div class="flex flex-row justify-between gap-2">
                    <div class="form-control w-2/5">
                        <label class="label opacity-70">
                        <span class="label-text">Name of Student</span>
                        </label>
                        <input name="student_name" type="text" x-model="student_name" class="input input-bordered w-full max-w-xs input-sm" required/>
                    </div>
                    <div class="form-control w-1/5">
                        <label class="label opacity-70">
                        <span class="label-text">Application Date</span>
                        </label>
                        <input name="application_date" type="text" placeholder="dd-mm-yyyy" x-model="application_date" class="input input-bordered w-full max-w-xs input-sm" pattern="[0-3][0-9]-[0-1][0-9]-[0-2][0-9][0-9][0-9]" required/>
                    </div>
                    <div x-data="{
                            isOld: false,
                        }"
                        class="w-2/5 flex flex-row justify-evenly items-center bg-base-200 p-2 rounded-md gap-2">
                        <div>
                            Is old?
                            <input x-model="isOld" type="checkbox" class="checkbox checkbox-primary">
                        </div>
                        <div class="flex-grow form-control w-1/3">
                            <label class="label opacity-70">
                            <span class="label-text">Application No.</span>
                            </label>
                            <input name="application_no" type="text" x-model="application_no" class="input input-bordered w-full m ax-w-xs input-sm read-only:bg-base-200" :required="isOld" :readonly="!isOld"/>
                        </div>
                    </div>
                </div>
                <fieldset class="flex flex-row p-2 my-2 py-4 space-x-2 border border-base-content border-opacity-10 rounded-md w-full">
                    <legend>Passed Exam Details</legend>
                    <div class="form-control w-1/6">
                        <label class="label opacity-70">
                        <span class="label-text">Name of Exam</span>
                        </label>
                        <input name="passed_exam_details[exam_name]" type="text" x-model="passed_exam_details.exam_name" class="input input-bordered w-full max-w-xs input-sm" />
                    </div>
                    <div class="form-control w-1/6">
                        <label class="label opacity-70">
                        <span class="label-text">Register No.</span>
                        </label>
                        <input name="passed_exam_details[exam_reg_no]" type="text" x-model="passed_exam_details.reg_no" class="input input-bordered w-full max-w-xs input-sm" />
                    </div>
                    <div class="form-control flex-grow">
                        <label class="label opacity-70">
                        <span class="label-text">Institution name & address</span>
                        </label>
                        <textarea name="passed_exam_details[institution]" x-model="passed_exam_details.insitution" class="textarea textarea-sm textarea-bordered h-16 max-w-xs"></textarea>
                    </div>
                    <div class="form-control w-1/5">
                        <label class="label opacity-70">
                        <span class="label-text">Affiliated Board</span>
                        </label>
                        <input name="passed_exam_details[affilated_board]" type="text" x-model="passed_exam_details.affiliated_board" class="input input-bordered w-full max-w-xs input-sm" />
                    </div>
                    <div class="form-control w-1/6">
                        <label class="label opacity-70">
                        <span class="label-text">Exam Start Date</span>
                        </label>
                        <input name="passed_exam_details[exam_start_date]" type="text" x-model="passed_exam_details.exam_start_date" placeholder="dd-mm-yyyy" class="input input-bordered w-full max-w-xs input-sm" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]"/>
                    </div>
                </fieldset>
                <div class="form-control w-1/3">
                    <label class="label opacity-70">
                    <span class="label-text">Arrears in Annual Subscription On Exam Date (No. of months, if any)</span>
                    </label>
                    <input name="arrear_months_exdt" type="number" x-model="arrears_months" class="input input-bordered w-full max-w-xs input-sm" required/>
                </div>
                <div class="form-control w-1/3">
                    <label class="label opacity-70">
                    <span class="label-text">Whether belonging to SC/ST category?</span>
                    </label>
                    <select name="is_sc_st" x-model="is_sc_st" class="select select-sm py-0 select-bordered w-24">
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>
                <div class="my-8">
                    <label class="label opacity-70">
                        <span class="label-text">Marks scored by the student:</span>
                    </label>
                    <table class="table table-compact w-full">
                        <thead>
                            <tr>
                                <td>Subject</td>
                                <td>Marks Scored</td>
                                <td>Maximum Mark</td>
                                <td>Points</td>
                                <td>Percentage</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(m, i) in marks">
                                <tr>
                                    <td>
                                        <input :name="'marks_scored['+i+'][subject]'" type="text" x-model="m.subject" class="input input-bordered w-full max-w-xs input-sm" />
                                    </td>
                                    <td>
                                        <input :name="'marks_scored['+i+'][marks_scored]'" type="text" x-model="m.marks_scored" class="input input-bordered w-full max-w-xs input-sm"  @change="setPercentage(m)"/>
                                    </td>
                                    <td>
                                        <input :name="'marks_scored['+i+'][max_mark]'" type="text" x-model="m.max_mark" class="input input-bordered w-full max-w-xs input-sm" @change="setPercentage(m)" />
                                    </td>
                                    <td>
                                        <input :name="'marks_scored['+i+'][points]'" type="text" x-model="m.points" class="input input-bordered w-full max-w-xs input-sm"/>
                                    </td>
                                    <td>
                                        <input :name="'marks_scored['+i+'][percentage]'" type="text" x-model="m.percentage" class="input input-bordered w-full max-w-xs input-sm" readonly/>
                                    </td>
                                    <td>
                                        <button x-show="i == marks.length - 1" type="button" class="btn btn-sm btn-warning" @click.prevent.stop="addSubject();">
                                            <x-easyadmin::display.icon icon="easyadmin::icons.plus"/>
                                        </button>
                                        <button x-show="marks.length > 1" type="button" class="btn btn-sm btn-error" @click.prevent.stop="removeSubject(i);">
                                            <x-easyadmin::display.icon icon="easyadmin::icons.delete"/>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tbody>
                            <tr>
                                <td class="font-bold bg-base-200">Total</td>
                                <td class="font-bold bg-base-200">
                                    <input :name="'marks_total[total_scored]'" type="text" x-model="marks_total.total_scored" class="input input-bordered w-full max-w-xs input-sm" readonly/>
                                </td>
                                <td class="font-bold bg-base-200">
                                    <input :name="'marks_total[total_max]'" type="text" x-model="marks_total.total_max" class="input input-bordered w-full max-w-xs input-sm" readonly/>
                                </td>
                                <td class="font-bold bg-base-200">
                                    <input :name="'marks_total[points]'" type="text" x-model="marks_total.points" class="input input-bordered w-full max-w-xs input-sm" readonly/>
                                </td>
                                <td class="font-bold bg-base-200">
                                    <input :name="'marks_total[percentage]'" type="text" x-model="marks_total.percentage" class="input input-bordered w-full max-w-xs input-sm" readonly/>
                                </td>
                                <td class="font-bold bg-base-200"></td>
                            </tr>
                        </tbody>
                    </table>
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
                    <div class="w-1/3 p-4">
                        <x-easyadmin::inputs.imageuploader :element="[
                            'key' => 'mark_list',
                            'authorised' => true,
                            'label' => 'Mark List',
                            'validations' => [
                                'mime_types' => ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'],
                                'max_size' => '200 kb'
                            ]
                        ]"/>
                    </div>
                    <div class="w-1/3 p-4">
                        <x-easyadmin::inputs.imageuploader :element="[
                            'key' => 'tc',
                            'authorised' => true,
                            'label' => 'Transfer Certificate (TC)',
                            'validations' => [
                                'mime_types' => ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'],
                                'max_size' => '200 kb'
                            ]
                        ]"/>
                    </div>
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
                    <div class="w-1/3 p-4">
                        <x-easyadmin::inputs.imageuploader :element="[
                            'key' => 'union_certificate',
                            'authorised' => true,
                            'label' => 'Proof of membership (Certificate by Union)',
                            'validations' => [
                                'mime_types' => ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'],
                                'max_size' => '200 kb'
                            ]
                        ]"/>
                    </div>
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
                            'key' => 'caste_certificate',
                            'authorised' => true,
                            'label' => 'Caste Certificate',
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
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-sm btn-primary min-w-24">Submit</button>
                </div>
            </form>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>
