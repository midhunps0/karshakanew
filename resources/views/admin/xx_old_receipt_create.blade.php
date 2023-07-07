<x-easyadmin::partials.adminpanel>
    <h3 class="text-xl font-bold pb-3"><span>Enter Old Receipt</span>&nbsp;</h3>
    @php
        $feeTypes = \App\Models\FeeType::all()->pluck('name', 'id');
        $typesWithTenure = config('generalSettings.fee_types_with_tenure');
    @endphp
    <div class="w-full"
        x-data="{
            member: null,
            date: null,
            bookNo: null,
            receiptNo: null,
            fees: [
                {
                    particulars: '',
                    tenure: null,
                    from: null,
                    to: null,
                    amount: null,
                    history: true,
                }
            ],
            notes: '',
            typesWithTenure: [],
            remarks: '',
            postUrl: '',
            total() {
                return this.fees.reduce((r, f) => {
                    return parseInt(r) + (f.amount != null ? parseInt(f.amount) : 0);
                }, 0);
            },
            addFeeItem() {
                this.fees.push({
                    particulars: '',
                    tenure: null,
                    from: null,
                    to: null,
                    amount: null,
                    history: true,
                });
            },
            removeFeeItem(index) {
                this.fees = this.fees.filter((f, i) => {
                    if (this.fees.length == 1 || i != index) {
                        return f;
                    }
                });
            },
            fetchMember(id) {
                console.log('id: '+id);
                axios.get(
                    '{{route('members.fetch', '_X_')}}'.replace('_X_', id)
                ).then((r) => {
                    this.member = r.data.member;
                    this.fees = [
                        {
                            particulars: '',
                            tenure: null,
                            from: null,
                            to: null,
                            amount: null,
                            history: true,
                        }
                    ];
                    console.log(this.member);
                }).catch((e) => {
                    console.log(e);
                })
            },
            editAction(id) {
                $dispatch(
                    'linkaction',
                    {
                        link: '{{route('members.edit', '_X_')}}'.replace('_X_', id),
                        route: 'members.edit',
                        fresh: true,
                        target: 'renderedpanel',
                    }
                );
            },
            getFromToMonths(index) {
                let f = this.fees.filter((f, i) => {
                    if (i == index) {
                        return f;
                    }
                })[0];
                axios.get(
                    '{{route('members.annual_fees.fromto', '_X_')}}'.replace('_X_', this.member.id),
                    {
                        params: {
                            tenure: f.tenure
                        }
                    }
                ).then((r) => {
                    if (r.data.from != null) {
                        this.fees = this.fees.map((f, i) => {
                            if (i == index) {
                                f.from = r.data.from;
                                f.to = r.data.to;
                            }
                            return f;
                        });
                    } else {
                        f.history = false;
                        if (f.from != null && f.from != '') {
                            f.to = this.getToDate(f.tenure, f.from);
                        }
                    }
                }).catch(
                    (e) => { console.log(e); }
                );
            },
            getToDate(tenure, from) {
                let ar = from.split('-');
                let dt = (new Date).set({ year: parseInt(ar[2]), month: parseInt(ar[1] - 1), day: parseInt(ar[0])})
                dt.addMonths(parseInt(tenure));
                dt.addDays(-1);
                return dt.toString('dd-MM-yyyy');
            },
            onFromChanged(index) {
                let f = this.fees.filter((f, i) => {
                    if (i == index) {
                        return f;
                    }
                })[0];
                if (f.tenure != null && f.tenure != '' && f.from != null && f.from != '') {
                    f.to = this.getToDate(f.tenure, f.from);
                }
            },
            doSubmit() {
                {{-- let form = document.getElementById('{{$form['id']}}'); --}}
                let fd = new FormData();
                let date = Date.parse(this.date);
                fd.append('date', date.toString('dd-MM-yyyy'));
                fd.append('book_no', this.bookNo);
                fd.append('receipt_no', this.receiptNo);
                this.fees.forEach((f, i) => {
                    fd.append('fee_item['+i+'][fee_type_id]', f.particulars);
                    if (f.tenure != null && f.tenure != '') {
                        fd.append('fee_item['+i+'][period_from]', f.from);
                        fd.append('fee_item['+i+'][period_to]', f.to);
                    }
                    fd.append('fee_item['+i+'][amount]', f.amount);
                });
                fd.append('notes', this.notes);
                {{-- fd.append('x_fr', 'form_user_create'); --}}
                {{-- fd.append('member_id', this.member.id); --}}
                $dispatch('formsubmit', { url: this.postUrl.replace('_X_', this.member.id), formData: fd, target: '{{$form['id']}}', fragment: 'create_form' });
            }
        }">
        <x-utils.memberfinder />
        <div x-show="member != null">
            <h3 class="text-sm font-bold pb-3 text-warning">Create Receipt For:</h3>
            <div class="flex flex-row space-x-8 p-4 border border-base-content border-opacity-20  rounded-md bg-base-200">
                <div class="p-0 min-w-72">
                    <span class="font-bold">Name</span>:&nbsp;
                    <span class="text-xl font-bold" x-text="member != null ? member.name : ''"></span>
                </div>
                <div class="p-0 min-w-72">
                    <span class="font-bold">Reg. No.</span>:&nbsp;
                    <span class="text-xl font-bold" x-text="member != null ? member.membership_no : ''"></span>
                </div>
                <div class="p-0 min-w-72">
                    <span class="font-bold">Reg. Date</span>:&nbsp;
                    <span class="text-xl font-bold" x-text="member != null ? member.approved_at : ''"></span>
                </div>
                <div>
                    <a href="" @click.prevent.stop="editAction(member.id);"
                        class="btn btn-sm btn-warning">Edit <x-easyadmin::display.icon icon="easyadmin::icons.edit" height="h-4" width="w-4"/></a>
                </div>
            </div>
        </div>
        <form
            @submit.prevent.stop="doSubmit();"
                @formresponse.window="console.log($event.detail);
                if ($event.detail.target == $el.id) {
                    if ($event.detail.content.success) {
                        $dispatch('shownotice', {message: $event.detail.content.message, mode: 'success', redirectUrl: successRedirectUrl, redirectRoute: successRedirectRoute});
                        $dispatch('formerrors', {errors: []});
                    } else if (typeof $event.detail.content.errors != undefined) {
                        $dispatch('formerrors', {errors: $event.detail.content.errors});
                    } else{
                        $dispatch('shownotice', {message: $event.detail.content.error, mode: 'error', redirectUrl: null, redirectRoute: null});
                    }
                }
            "
            @datepicker.window="console.log($event);date = $event.detail.value;"
            @selectmember.window="fetchMember($event.detail.id);"
            class="p-1" action=""
            id="{{$form['id']}}"
            x-init="
            postUrl='{{route('members.old_fees.store', '_X_')}}'
            @foreach ($typesWithTenure as $t)
                typesWithTenure.push(parseInt({{$t}}));
            @endforeach
            console.log(typesWithTenure);
            "
            >
            <div x-show="member != null" class="">
                {{-- <h3 class="text-sm font-bold pb-3 text-warning">Create Receipt For:</h3>
                <div class="flex flex-row space-x-8 p-4 border border-base-content border-opacity-20  rounded-md bg-base-200">
                    <div class="p-0 min-w-72">
                        <span class="font-bold">Name</span>:&nbsp;
                        <span class="text-xl font-bold" x-text="member != null ? member.name : ''"></span>
                    </div>
                    <div class="p-0 min-w-72">
                        <span class="font-bold">Reg. No.</span>:&nbsp;
                        <span class="text-xl font-bold" x-text="member != null ? member.membership_no : ''"></span>
                    </div>
                    <div class="p-0 min-w-72">
                        <span class="font-bold">Reg. Date</span>:&nbsp;
                        <span class="text-xl font-bold" x-text="member != null ? member.approved_at : ''"></span>
                    </div>
                    <div>
                        <a href="" @click.prevent.stop="editAction(member.id);"
                            class="btn btn-sm btn-warning">Edit <x-easyadmin::display.icon icon="easyadmin::icons.edit" height="h-4" width="w-4"/></a>
                    </div>
                </div> --}}
                <div class="my-4">
                    <div class="p-4 border border-base-content border-opacity-20 rounded-md bg-base-200">
                        <form action="">
                            <h3 class="text-md font-bold my-3 text-center underline text-warning"><span>Receipt Details</span>&nbsp;</h3>
                            <div class="flex flex-row justify-between mt-3 mb-6">
                                <div>
                                    <x-inputs.datepicker :element="[
                                        'key' => 'date',
                                        'start_year' => 1980,
                                        'end_year' => \Illuminate\Support\Carbon::now()->year,
                                        'date_format' => 'dd-mm-yyyy',
                                        'authorised' => true,
                                        'label' => 'Date',
                                        'properties' => ['required' => true]
                                    ]"
                                    label_position="float"
                                    />
                                    {{-- <input type="text" class="input input-bordered input-sm"> --}}
                                </div>
                                <div>
                                    Book No.:<span class="text-warning">*</span>
                                    <input x-model="bookNo" type="text" class="input input-bordered input-md">
                                </div>
                                <div>
                                    Receipt No.:<span class="text-warning">*</span>
                                    <input x-model="receiptNo" type="text" class="input input-bordered input-md">
                                </div>
                            </div>
                            <table class="text-sm table table-compact mx-auto w-full">
                                <tr>
                                    <td class="text-center">Particulars</td>
                                    <td class="text-center">Tenure<br>(No. of Months)</td>
                                    <td class="text-center">From<br>(dd-mm-yyyy)</td>
                                    <td class="text-center">To<br>(dd-mm-yyyy)</td>
                                    <td class="text-center">Amount<br>(Rs.)</td>
                                    <td></td>
                                </tr>
                                <template x-for="(fee, i) in fees">
                                    <tr>
                                        <td class="w-52">
                                            <select :name="'fee_type_id['+i+']'" class="select select-bordered w-full max-w-xs" x-model="fee.particulars" required>
                                                <option value="" disabled selected>Select One</option>
                                                @foreach ($feeTypes as $id => $name)
                                                    <option value="{{$id}}">{{$name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="">
                                            <div>
                                                <input :name="'tenure['+i+']'" class="input input-md input-bordered" type="text" x-model="fee.tenure" :disabled="!typesWithTenure.includes(parseInt(fee.particulars))"
                                                @input="getFromToMonths(i);">
                                            </div>
                                        </td>
                                        <td class="">
                                            <input :name="'period_from['+i+']'" class="input input-md input-bordered" type="text" x-model="fee.from" :required="typesWithTenure.includes(parseInt(fee.particulars))" :disabled="fee.history" required pattern="[0-3][0-9]-[0-1][0-9]-[0-2][0-9][0-9][0-9]" @change="onFromChanged(i);">
                                        </td>
                                        <td class="">
                                            <input :name="'period_to['+i+']'" class="input input-md input-bordered" type="text" x-model="fee.to" disabled :required="typesWithTenure.includes(parseInt(fee.particulars))"  pattern="[0-3][0-9]-[0-1][0-9]-[0-2][0-9][0-9][0-9]">
                                        </td>
                                        <td class="">
                                            <input :name="'amount['+i+']'" class="input input-md input-bordered" type="text" x-model="fee.amount" required>
                                        </td>
                                        <td>
                                            <div class="flex flex-row" :class="i != (fees.length - 1) || 'space-x-2'">
                                                <button x-show="i == (fees.length - 1)" class="btn btn-sm btn-warning" @click.prevent.stop="addFeeItem();">
                                                    <x-easyadmin::display.icon icon="easyadmin::icons.plus" height="h-4" width="w-4" />
                                                </button>
                                                <button class="btn btn-sm btn-error"
                                                    @click.prevent.stop="removeFeeItem(i);" :disabled="fees.length == 1">
                                                    <x-easyadmin::display.icon icon="easyadmin::icons.delete" height="h-4" width="w-4"/>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <tr>
                                    <td colspan=4 class="text-right">
                                        <span>Total:&nbsp;</span>
                                    </td>
                                    <td>
                                        <input :value="total()" class="input input-md input-bordered" type="text" disabled>
                                    </td>
                                    <td></td>
                                </tr>
                            </table>
                            <div class="divider"></div>
                            <div class="flex flex-wrap space-x-4 items-end">
                                <textarea x-model="notes" class="textarea textarea-bordered w-3/4" placeholder="Notes"></textarea>
                                <button class="btn btn-md btn-warning flex-grow"> Create Receipt </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </form>
        <template x-if="member != null">
        <div class="my-4">
            <h3 class="text-md font-bold my-3 text-center underline text-warning"><span>Transaction History</span>&nbsp;</h3>
            <template x-if="member.fee_payments.length == 0">
                <div class="text-center text-error">There is no transaction made by this member.</div>
            </template>
            <template x-if="member.fee_payments.length > 0">
            <table class="table table-compact table-zebra w-full mx-auto">
                <tr>
                    <td>Date</td>
                    <td>Particulars</td>
                    <td>Amount</td>
                    <td>From</td>
                    <td>To</td>
                    <td>Tenure</td>
                    <td>Notes</td>
                </tr>
                <template x-for="fp in member.fee_payments">
                    <template x-for="item in fp.fee_items">
                        <tr>
                            <td><span x-text="fp.payment_date"></span></td>
                            <td><span x-text="item.fee_type.name"></span></td>
                            <td><span x-text="item.amount"></span></td>
                            <td><span x-text="item.period_from"></span></td>
                            <td><span x-text="item.period_to"></span></td>
                            <td><span x-text="item.tenure"></span></td>
                            <td><span x-text="fp.notes"></span></td>
                        </tr>
                    </template>
                </template>
            </table>
            </template>
        </div>
        </template>
    </div>
</x-easyadmin::partials.adminpanel>
