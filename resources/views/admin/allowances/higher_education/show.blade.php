<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Allowance Application</span>&nbsp;</h3>
        <div class="text-right p-4 flex flex-row justify-between">
            @if(isset($application) && $application->editable_by_status)
            <a href="" class="btn btn-sm btn-warning" @click.prevent.stop="$dispatch('linkaction', {link: '{{route('allowances.education.edit', $application->id)}}'});" >Edit</a>
            @else
            <span class="btn btn-sm btn-disabled">Edit</span>
            @endif
            <a href="" class="btn btn-sm" @click.prevent.stop="history.back();" >Back</a>
        </div>
        @if (isset($error))
        <div class="my-2 border border-base-content border-opacity-20 rounded-md text-center py-4">
            <span class="text-error">{{$error}}</span>
        </div>
        @else
        <h4 class="my-4"><span class="font-bold opacity-60">Member Details:</span></h4>
        <div class="my-2 flex flex-row justify-between border border-base-content border-opacity-20 rounded-md">
            <div class="my-1 p-2">
                <span class="font-bold opacity-60">Name:</span>&nbsp;
                <span>{{$application->member->display_name}}</span>&nbsp;
                <a href="" @click.prevent.stop="$dispatch('linkaction', { link: '{{route('members.show', $application->member->id)}}', route: 'members.show'})" class="text-warning"><x-easyadmin::display.icon icon="easyadmin::icons.view_on" height="h-4" width="w-4"/></a>
            </div>
            <div class="my-2 p-2"><span class="font-bold opacity-60">Membership No.:</span>&nbsp;<span>{{$application->member->membership_no}}</span></div>
            <div class="my-2 p-2"><span class="font-bold opacity-60">Aadhaar No.:</span>&nbsp;<span>{{$application->member->aadhaar_no}}</span></div>
        </div>
        <h4 class="my-4 text-center text-2xl underline mt-8"><span class="font-bold opacity-80">Application Details:</span></h4>
        @if (isset($application->allowanceable))
            <div class="flex flex-row justify-between p-3 border border-base-content border-opacity-10 rounded-md mb-4">
                <div class="my-2 py-2 text-xl"><span class="font-bold opacity-60">Scheme:</span>&nbsp;<span>{{$application->welfareScheme->name}}</span></div>
                <div class="my-2 py-2 text-xl"><span class="font-bold opacity-60">Appl. No.:</span>&nbsp;<span>{{$application->application_no}}</span></div>
                <div class="my-2 py-2 text-xl"><span class="font-bold opacity-60">Appl. Date:</span>&nbsp;<span>{{$application->application_date}}</span></div>
            </div>
            <div class="flex flex-row flex-wrap">
                <div class="my-2 p-y2 w-1/2"><span class="font-bold opacity-60">Member's Name:</span>&nbsp;<span>{{$application->allowanceable->member_name}}</span></div>
                <div class="my-2 p-y2 w-1/2"><span class="font-bold opacity-60">Member's Address:</span>&nbsp;<span>{{$application->allowanceable->member_address}}</span></div>
                <div class="my-2 p-y2 w-1/2"><span class="font-bold opacity-60">Member's Aadhaar No.:</span>&nbsp;<span>{{$application->allowanceable->member_aadhaar}}</span></div>
                <div class="my-2 p-y2 w-1/2"><span class="font-bold opacity-60">Member's Phone No.:</span>&nbsp;<span>{{$application->allowanceable->member_phone}}</span></div>

            </div>
            <div>
                <h4 class="my-4"><span class="font-bold opacity-60">Bank Details:</span></h4>
                <div class="border border-base-content border-opacity-20 rounded-md">
                    <table class="table table-compact w-full">
                        <thead>
                            <tr>
                                <td class="opacity-60">Account No.</td>
                                <td class="opacity-60">Bank Name</td>
                                <td class="opacity-60">Bank Branch</td>
                                <td class="opacity-60">IFSC Code</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{$application->allowanceable->member_bank_account['account_no']}}</td>
                                <td>{{$application->allowanceable->member_bank_account['bank_name']}}</td>
                                <td>{{$application->allowanceable->member_bank_account['bank_branch']}}</td>
                                <td>{{$application->allowanceable->member_bank_account['ifsc_code']}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="my-4">
                    <div class="my-2 py-2 w-1/2"><span class="font-bold opacity-60">Student's Name:</span>&nbsp;<span>{{$application->allowanceable->student_name}}</span></div>
                </div>
                <div class="my-4">
                    <div class="my-2 w-1/2"><span class="font-bold opacity-60">Exam Details:</div>
                    <div class="border border-base-content border-opacity-20 rounded-md">
                        <table class="table table-compact w-full">
                            <thead>
                                <tr>
                                    <td class="opacity-60">Exam</td>
                                    <td class="opacity-60">Reg. No.</td>
                                    <td class="opacity-60">Institution</td>
                                    <td class="opacity-60">Affiliated Board</td>
                                    <td class="opacity-60">Exam Start Date</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{$application->allowanceable->passed_exam_details['exam_name']}}</td>
                                    <td>{{$application->allowanceable->passed_exam_details['exam_reg_no']}}</td>
                                    <td>{{$application->allowanceable->passed_exam_details['institution']}}</td>
                                    <td>{{$application->allowanceable->passed_exam_details['affilated_board']}}</td>
                                    <td>{{$application->allowanceable->passed_exam_details['exam_start_date']}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="my-4">
                    <div class="my-2 py-2 w-1/2"><span class="font-bold opacity-60">No. of months of arrears:</span>&nbsp;<span>{{$application->allowanceable->arrear_months_exdt}}</span></div>
                </div>
                <div class="my-4">
                    <div class="my-2 w-1/2"><span class="font-bold opacity-60">Marks scored by the student:</div>
                    <div class="border border-base-content border-opacity-20 rounded-md">
                        <table class="table table-compact w-full">
                            <thead>
                                <tr>
                                    <td class="opacity-60">Subject</td>
                                    <td class="opacity-60">Marks Scored</td>
                                    <td class="opacity-60">Maximum Marks</td>
                                    <td class="opacity-60">Points (If, applicable)</td>
                                    <td class="opacity-60">Percentage</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($application->allowanceable->marks_scored as $m)
                                <tr>
                                    <td>{{$m['subject']}}</td>
                                    <td>{{$m['marks_scored'] ?? ''}}</td>
                                    <td>{{$m['max_mark'] ?? ''}}</td>
                                    <td>{{$m['points'] ?? ''}}</td>
                                    <td>{{$m['percentage'] ?? ''}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tbody>
                                <tr class="font-bold">
                                    <td>Total</td>
                                    <td>{{$application->allowanceable->total_marks['total_scored'] ?? ''}}</td>
                                    <td>{{$application->allowanceable->total_marks['total_max'] ?? ''}}</td>
                                    <td>{{$application->allowanceable->total_marks['points'] ?? ''}}</td>
                                    <td>{{$application->allowanceable->total_marks['percentage'] ?? ''}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="my-4">
                    <div class="my-2 w-1/2"><span class="font-bold opacity-60">Attachments:</div>
                        <div class="flex flex-row flex-wrap items-start justify-start p-3">
                            <div class="md:w-1/5 p-1 my-1">
                                <span class="text-warning">Mark List:</span>
                                <div class="block m-2 w-32 h-24">
                                    @if ($application->allowanceable->getSingleMediaForDisplay('mark_list') != null)
                                    <img @click="$dispatch('showimg', {src: '{{$application->allowanceable->getSingleMediaForDisplay('mark_list')['path']}}'});" class="cursor-pointer max-h-full max-w-full hover:scale-110 transition-transform" src="{{$application->allowanceable->getSingleMediaForDisplay('mark_list')['path']}}" />
                                    @else
                                    <span class="text-error text-opacity-80">Not submitted</span>
                                    @endif
                                </div>
                            </div>
                            <div class="md:w-1/5 p-1 my-1">
                                <span class="text-warning">Transfer Certificate:</span>
                                <div class="block m-2 w-32 h-24">
                                    @if ($application->allowanceable->getSingleMediaForDisplay('tc') != null)
                                    <img @click="$dispatch('showimg', {src: '{{$application->allowanceable->getSingleMediaForDisplay('tc')['path']}}'});" class="cursor-pointer max-h-full max-w-full hover:scale-110 transition-transform" src="{{$application->allowanceable->getSingleMediaForDisplay('tc')['path']}}" />
                                    @else
                                    <span class="text-error text-opacity-80">Not submitted</span>
                                    @endif
                                </div>
                            </div>
                            <div class="md:w-1/5 p-1 my-1">
                                <span class="text-warning">Passbook Front:</span>
                                <div class="block m-2 w-32 h-24">
                                    @if ($application->allowanceable->getSingleMediaForDisplay('wb_passbook_front') != null)
                                    <img @click="$dispatch('showimg', {src: '{{$application->allowanceable->getSingleMediaForDisplay('wb_passbook_front')['path']}}'});" class="cursor-pointer max-h-full max-w-full hover:scale-110 transition-transform" src="{{$application->allowanceable->getSingleMediaForDisplay('wb_passbook_front')['path']}}" />
                                    @else
                                    <span class="text-error text-opacity-80">Not submitted</span>
                                    @endif
                                </div>
                            </div>
                            <div class="md:w-1/5 p-1 my-1">
                                <span class="text-warning">Passbook Back:</span>
                                <div class="block m-2 w-32 h-24">
                                    @if ($application->allowanceable->getSingleMediaForDisplay('wb_passbook_back') != null)
                                    <img @click="$dispatch('showimg', {src: '{{$application->allowanceable->getSingleMediaForDisplay('wb_passbook_back')['path']}}'});" class="cursor-pointer max-h-full max-w-full hover:scale-110 transition-transform" src="{{$application->allowanceable->getSingleMediaForDisplay('wb_passbook_back')['path']}}" />
                                    @else
                                    <span class="text-error text-opacity-80">Not submitted</span>
                                    @endif
                                </div>
                            </div>
                            <div class="md:w-1/5 p-1 my-1">
                                <span class="text-warning">Aadhaar Card:</span>
                                <div class="block m-2 w-32 h-24">
                                    @if ($application->allowanceable->getSingleMediaForDisplay('aadhaar_card') != null)
                                    <img @click="$dispatch('showimg', {src: '{{$application->allowanceable->getSingleMediaForDisplay('aadhaar_card')['path']}}'});" class="cursor-pointer max-h-full max-w-full hover:scale-110 transition-transform" src="{{$application->allowanceable->getSingleMediaForDisplay('aadhaar_card')['path']}}" />
                                    @else
                                    <span class="text-error text-opacity-80">Not submitted</span>
                                    @endif
                                </div>
                            </div>
                            <div class="md:w-1/5 p-1 my-1">
                                <span class="text-warning">Bank Passbook:</span>
                                <div class="block m-2 w-32 h-24">
                                    @if ($application->allowanceable->getSingleMediaForDisplay('bank_passbook') != null)
                                    <img @click="$dispatch('showimg', {src: '{{$application->allowanceable->getSingleMediaForDisplay('bank_passbook')['path']}}'});" class="cursor-pointer max-h-full max-w-full hover:scale-110 transition-transform" src="{{$application->allowanceable->getSingleMediaForDisplay('bank_passbook')['path']}}" />
                                    @else
                                    <span class="text-error text-opacity-80">Not submitted</span>
                                    @endif
                                </div>
                            </div>
                            <div class="md:w-1/5 p-1 my-1">
                                <span class="text-warning">Union Certificate:</span>
                                <div class="block m-2 w-32 h-24">
                                    @if ($application->allowanceable->getSingleMediaForDisplay('union_certificate') != null)
                                    <img @click="$dispatch('showimg', {src: '{{$application->allowanceable->getSingleMediaForDisplay('union_certificate')['path']}}'});" class="cursor-pointer max-h-full max-w-full hover:scale-110 transition-transform" src="{{$application->allowanceable->getSingleMediaForDisplay('union_certificate')['path']}}" />
                                    @else
                                    <span class="text-error text-opacity-80">Not submitted</span>
                                    @endif
                                </div>
                            </div>
                            <div class="md:w-1/5 p-1 my-1">
                                <span class="text-warning">Ration Card:</span>
                                <div class="block m-2 w-32 h-24">
                                    @if ($application->allowanceable->getSingleMediaForDisplay('ration_card') != null)
                                    <img @click="$dispatch('showimg', {src: '{{$application->allowanceable->getSingleMediaForDisplay('ration_card')['path']}}'});" class="cursor-pointer max-h-full max-w-full hover:scale-110 transition-transform" src="{{$application->allowanceable->getSingleMediaForDisplay('ration_card')['path']}}" />
                                    @else
                                    <span class="text-error text-opacity-80">Not submitted</span>
                                    @endif
                                </div>
                            </div>
                            <div class="md:w-1/5 p-1 my-1">
                                <span class="text-warning">Caste Certificate:</span>
                                <div class="block m-2 w-32 h-24">
                                    @if ($application->allowanceable->getSingleMediaForDisplay('caste_certificate') != null)
                                    <img @click="$dispatch('showimg', {src: '{{$application->allowanceable->getSingleMediaForDisplay('caste_certificate')['path']}}'});" class="cursor-pointer max-h-full max-w-full hover:scale-110 transition-transform" src="{{$application->allowanceable->getSingleMediaForDisplay('caste_certificate')['path']}}" />
                                    @else
                                    <span class="text-error text-opacity-80">Not submitted</span>
                                    @endif
                                </div>
                            </div>
                            <div class="md:w-1/5 p-1 my-1">
                                <span class="text-warning">One And Same Certificate:</span>
                                <div class="block m-2 w-32 h-24">
                                    @if ($application->allowanceable->getSingleMediaForDisplay('one_and_same_certificate') != null)
                                    <img @click="$dispatch('showimg', {src: '{{$application->allowanceable->getSingleMediaForDisplay('one_and_same_certificate')['path']}}'});" class="cursor-pointer max-h-full max-w-full hover:scale-110 transition-transform" src="{{$application->allowanceable->getSingleMediaForDisplay('one_and_same_certificate')['path']}}" />
                                    @else
                                    <span class="text-error text-opacity-80">Not submitted</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div x-show="showImg" x-transition x-data="{
                    imgSrc: '',
                    showImg: false
                }"
                @showimg.window="imgSrc = $event.detail.src; showImg = true;"
                class="fixed top-0 left-0 z-50 w-full h-full flex flex-row justify-center items-center bg-base-200 bg-opacity-50 print:hidden"
                >
                <div class="max-w-full max-h-full relative bg-base-100 bg-opacity-100 border border-base-content border-opacity-20 rounded-lg p-4">
                    <button @click="showImg = false; imgSrc = '';" class="btn btn-error btn-sm absolute -top-14 -right-14">
                        <x-easyadmin::display.icon icon="easyadmin::icons.close"/>
                    </button>
                    <img :src="imgSrc" class="max-w-full max-h-full">
                </div>
            </div>
            <div x-data="{
                    status: '{{$application->status}}',
                    amount: '',
                    sanctioned_amount: {{$application->sanctioned_amount ?? 'null'}},
                    sanctioned_date: '{{$application->sanctioned_date}}',
                    rejection_reason: '{{\App\Helpers\AppHelper::jssafe($application->rejection_reason)}}',
                    statusClass() {
                        switch (this.status) {
                            case 'Pending':
                                return 'bg-base-200';
                                break;
                            case 'Approved':
                                return 'bg-success bg-opacity-30';
                                break;
                            case 'Paid':
                                return 'bg-success';
                                break;
                            case 'Rejected':
                                return 'bg-error';
                                break;
                        }
                    },

                    doSubmit(approval) {
                        let formData = new FormData();
                        formData.append('approval', approval);
                        if (approval == 'Approved') {
                            formData.append('amount', this.amount);
                        }
                        if (approval == 'Rejected') {
                            formData.append('rejection_reason', this.rejection_reason);
                        }
                        axios.post(
                            '{{route('allowances.approve', $application->id)}}',
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
                                let apr = 'Approved.';
                                let smode = 'success';
                                if (approval == 'Rejected') {
                                    apr = 'Rejected.';
                                    smode = 'error';
                                    this.status = 'Rejected';
                                } else {
                                    this.status = 'Approved';
                                    this.sanctioned_date = r.data.sanctioned_date;
                                    this.sanctioned_amount = r.data.sanctioned_amount;
                                }
                                $dispatch('showtoast', {message: 'Application ' + apr, mode: smode, });
                                $dispatch('applnapproved');
                                {{-- setTimeout(() => {
                                    $dispatch('linkaction', {link: '{{route('allowances.education.show', '_X_')}}'.replace('_X_', r.data.application.id), route: 'allowances.education.show'})
                                }, 500); --}}
                            } else {
                                $dispatch('shownotice', {message: 'Application ' + apr, mode: smode, });
                            }
                        }).catch((e) => {
                            console.log(e);
                        });
                    },
                    doReject() {}
                }"
                @applnstatusupdated.window="status = $event.detail.status;"
                >
                <template x-if="sanctioned_date.trim() != ''">
                    <div class="my-4 flex flex-row">
                        <div class="my-2 py-2 w-1/2"><span class="font-bold opacity-60">Sanctioned Amount:</span>&nbsp;<span x-text="sanctioned_amount"></span></div>
                        <div class="my-2 py-2 w-1/2"><span class="font-bold opacity-60">Sanctioned Date:</span>&nbsp;<span x-text="sanctioned_date"></span></div>
                    </div>
                </template>
                <div x-show="rejection_reason != ''" class="my-4 flex flex-row">
                    <div class="my-2 py-2 w-1/2"><span class="font-bold opacity-60">Reason for rejection:</span>&nbsp;<span x-text="rejection_reason"></span></div>
                </div>
                <div class="text-center border border-base-content border-opacity-20 rounded-md p-2 opacity-80" :class="statusClass()">
                    <h4 class="font-bold text-sm opacity-60 mb-2">Status</h4>
                    <div class="p-2 font-bold text-xl"><span x-text="status"></span></div>
                </div>
                @if($application->status == 'Pending' && auth()->user()->can('approve', $application))
                    <div class="">
                        <form x-show="status == 'Pending'" x-transition id="approval-form" action="" class="md:w-3/5 mx-auto flex flex-row rounded-md overflow-hidden my-8">
                            <div class="w-1/2 text-center flex flex-col bg-error bg-opacity-10 p-4">
                                <h4 class="text-lg opacity-60 mb-2 text-center font-bold">Reject</h4>
                                <div class="form-control w-full max-w-xs mb-4">
                                    <label class="label opacity-70">
                                    <span class="label-text">Reason for rejection</span>
                                    </label>
                                    <input x-model="rejection_reason" name="sanctioned_amount" type="text" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" step="0.01"/>
                                </div>
                                <div class="flex justify-center items-center flex-grow">
                                    <button :disabled="rejection_reason == ''"  class="btn btn-sm btn-error" type="button" @click.prevent.submit="doSubmit('Rejected')">Reject</button>
                                </div>
                            </div>
                            <div class="w-1/2 text-center flex flex-col bg-success bg-opacity-10 p-4">
                                <h4 class="text-lg opacity-60 mb-2 text-center font-bold">Approve</h4>
                                <div class="form-control w-full max-w-xs mb-4">
                                    <label class="label opacity-70">
                                    <span class="label-text">Sanctioned Amount</span>
                                    </label>
                                    <input x-model="amount" name="sanctioned_amount" type="number" class="input input-bordered w-full max-w-xs input-sm read-only:bg-base-200 read-only:bg-opacity-70" step="0.01" min="0"/>
                                </div>
                                <div class="form-control w-full max-w-xs flex flex-row justify-evenly">
                                    <button class="btn btn-sm btn-success" type="button" @click.prevent.submit="doSubmit('Approved')" :disabled="amount.length == 0">Approve</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                <form x-data="{
                        status: 'Pending',
                        paid: false,
                        doSubmit() {
                            let formData = new FormData();
                            formData.append('approval', 'Paid');
                            axios.post(
                                '{{route('allowances.approve', $application->id)}}',
                                formData,
                                {
                                    headers: {
                                        'Content-Type': 'multipart/form-data',
                                    },
                                }
                            ).then((r) => {
                                console.log(r);
                                if (r.data.success) {
                                    this.paid = true;
                                    $dispatch('showtoast', {message: 'Application marked as paid', mode: 'success', });
                                    $dispatch('applnstatusupdated', {status: 'Paid'});
                                    {{-- setTimeout(() => {
                                        $dispatch('linkaction', {link: '{{route('allowances.education.show', '_X_')}}'.replace('_X_', r.data.application.id), route: 'allowances.education.show'})
                                    }, 500); --}}
                                } else {
                                    $dispatch('shownotice', {message: 'Application updation failed', mode: 'error', });
                                }
                            }).catch((e) => {
                                console.log(e);
                            });
                        },
                    }
                    "
                    x-init="
                        status = '{{$application->status}}';
                    "
                    @applnapproved.window="
                        status='Approved';
                        window.scrollTo(0, document.body.scrollHeight);
                    "
                    x-transition action="" class="md:w-3/5 mx-auto flex flex-row justify-center rounded-md overflow-hidden my-8">
                    <Button x-show="status == 'Approved' && !paid" type="button" class="btn btn-sm btn-primary" @click.prevent.stop="doSubmit();">Mark As Paid</Button>
                </form>

            </div>
        @else
        <div class="border border-base-content border-opacity-20 rounded-md py-4 my-4">
            <div class="p-2 text-warning text-center">
                Details not available for this application.
            </div>
        </div>
        @endif
        @endif
    </div>
</x-easyadmin::partials.adminpanel>
