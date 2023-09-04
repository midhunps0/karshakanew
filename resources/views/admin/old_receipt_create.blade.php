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
            fc_id: null,
            receipt: {
                fee_items: []
            },
            showform: true,
            showreceipt: false,
            showFinder: true,
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
            fetchMember(id = null) {
                console.log('id: '+id);
                if(id == null) { id = this.member.id; }
                axios.get(
                    '{{route('members.fetch', '_X_')}}'.replace('_X_', id)
                ).then((r) => {
                    this.member = r.data.member;
                    this.receiptNo = null;
                    this.showFinder = false;
                    {{-- this.fees = [
                        {
                            particulars: '',
                            tenure: null,
                            from: null,
                            to: null,
                            amount: null,
                            history: true,
                        }
                    ]; --}}
                    {{-- $dispatch('easetdate', {date: null, key: 'date'}); --}}
                }).catch((e) => {
                    console.log(e);
                })
            },
            fetchReceipt(id = null) {
                if (id == null) {
                    id = this.receipt.id
                }
                axios.get(
                    '{{route('receipt.fetch', '_X_')}}'.replace('_X_', id)
                ).then((r) => {
                    this.receipt = r.data.receipt;
                    this.showform = false;
                    this.showreceipt = true;
                }).catch((e) => {
                    console.log(e);
                });
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
            formatDate(yyyyMMdd) {
                if (typeof yyyyMMdd == 'undefined' || yyyyMMdd == null) {
                    return '';
                }
                let ar = yyyyMMdd.split('-');
                return (new Date).set({ year: parseInt(ar[0]), month: parseInt(ar[1] - 1), day: parseInt(ar[2])}).toString('dd-MM-yyyy');
            },
            getToDate(tenure, from) {
                let ar = from.split('-');
                if (ar.length < 3 || parseInt(ar[2]) < 1000) {
                    return '';
                }
                try {
                    let dt = (new Date).set({ year: parseInt(ar[2]), month: parseInt(ar[1] - 1), day: parseInt(ar[0])})
                    dt.addMonths(parseInt(tenure));
                    dt.addDays(-1);
                    return dt.toString('dd-MM-yyyy');
                } catch(e) {
                    return '';
                }
            },
            formatFromDate(index) {
                let f = this.fees.filter((f, i) => {
                    if (i == index) {
                        return f;
                    }
                })[0];
                console.log(f);
                if (f.from == null || f.from == '' || f.from.length < 3) {
                    f.from = '01-';
                } else if (f.from.length > 0) {
                    let char = f.from.charAt(f.from.length - 1);
                    console.log('char: ' + char);
                    if (/^\d$/.test(char) == false && char != '-') {
                        console.log()
                        f.from = f.from.substring(0, f.from.length - 1);
                    }
                    if (char == '-' && f.from.length < 5) {
                        let ar = f.from.split('-');
                        if (ar[1].length == 1) {
                            f.from = f.from.substring(0, f.from.length - 1);
                        }
                    }
                }
                if (f.from.length >= 5) {
                    let ar = f.from.split('-');
                    if (ar[1].length > 1 && parseInt(ar[1]) > 12) {
                        ar[1] = 12;
                    }
                    f.from = ar.join('-');
                    if (f.from.substring(0,3) != '01-') {
                        f.from = '01-' + f.from.substring(3);
                    }
                    console.log('l: '+f.from.length);
                    if (f.from.length == 5) {
                        if (f.from.charAt(f.from.length - 1) != '-') {
                            f.from = f.from + '-';
                        } else {
                            f.from = f.from.substring(0, f.from.length - 1);
                        }
                    }
                    f.from.replace('--', '-');
                }
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
            printReceipt() {
                let divContents = document.getElementById('receipt').innerHTML;
                let a = window.open('', '', 'height=500, width=500');
                a.document.write('<html>');
                a.document.write('<body>');
                a.document.write(divContents);
                a.document.write('</body></html>');
                a.document.close();
                a.print();
            },
            newReceipt() {
                this.showreceipt = false;
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
                this.showform = true;
            },
            close() {
                this.showreceipt = false;
                this.showform = true;
                this.showFinder = true;
                {{-- $dispatch('linkaction', {link: '{{route('feecollections.create')}}', route: 'feecollections.create'}); --}}
            },
            doSubmit() {
                {{-- let form = document.getElementById('{{$form['id']}}'); --}}
                let fd = new FormData();
                {{-- let date = Date.parse(this.date); --}}
                fd.append('book_number', this.bookNo);
                fd.append('receipt_number', this.receiptNo);
                fd.append('date', this.date);
                {{-- fd.append('date', date.toString('dd-MM-yyyy')); --}}
                this.fees.forEach((f, i) => {
                    fd.append('fee_item['+i+'][fee_type_id]', f.particulars);
                    if (f.from != null && f.to != null) {
                        fd.append('fee_item['+i+'][period_from]', f.from);
                        fd.append('fee_item['+i+'][period_to]', f.to);
                    }
                    fd.append('fee_item['+i+'][amount]', f.amount);
                });
                if (this.notes != '') {fd.append('notes', this.notes);}
                {{-- fd.append('x_fr', 'form_user_create'); --}}
                {{-- fd.append('member_id', this.member.id); --}}
                $dispatch('formsubmit', { url: this.postUrl.replace('_X_', this.member.id), formData: fd, target: '{{$form['id']}}', fragment: 'create_form' });
            }
        }"
        >
        <div x-show="showFinder">
            <x-utils.memberfinder />
        </div>
        <div x-show="member != null && !showFinder">
            <h3 class="text-sm font-bold pb-3 text-warning">Create Receipt For:</h3>
            <div class="flex flex-row flex-wrap space-x-0 space-y-2 md:space-y-0 md:space-x-8 p-4 border border-base-content border-opacity-20  rounded-md bg-base-200">
                <div class="p-0 min-w-72">
                    <span class="font-bold">Name</span>:&nbsp;
                    <span class="md:text-xl font-bold" x-text="member != null ? member.display_name : ''"></span>
                </div>
                <div class="p-0 min-w-72">
                    <span class="font-bold">Reg. No.</span>:&nbsp;
                    <span class="md:text-xl font-bold" x-text="member != null ? member.membership_no : ''"></span>
                </div>
                <div class="p-0 min-w-72">
                    <span class="font-bold">Reg. Date</span>:&nbsp;
                    <span class="md:text-xl font-bold" x-text="member != null ? member.reg_date : ''"></span>
                </div>
                <div class="flex-grow text-right">
                    <a href="" @click.prevent.stop="editAction(member.id);"
                        class="btn btn-sm btn-warning">Edit <x-easyadmin::display.icon icon="easyadmin::icons.edit" height="h-4" width="w-4"/></a>
                    <button type="button" class="btn btn-sm btn-error" @click.prevent.stop="showFinder = true;">Cancel</button>
                </div>
            </div>
        </div>
        <form x-show="showform && !showFinder"
            @submit.prevent.stop="doSubmit();"
                @formresponse.window="console.log($event.detail);
                console.log('fr captured');
                if ($event.detail.target == $el.id) {
                    if ($event.detail.content.success) {
                        receipt = $event.detail.content.receipt;
                        console.log(receipt);
                        showform = false;
                        showreceipt = true;
                        {{-- fetchMember(); --}}
                        member = null;
                        $dispatch('showtoast', {message: 'Receipt Created', mode: 'success', });
                        $dispatch('formerrors', {errors: []});
                    } else if (typeof $event.detail.content.errors != 'undefined') {
                        console.log('errors');
                        console.log($event.detail.content.errors);
                        if (typeof $event.detail.content.errors.receipt_number != 'undefined') {
                            $dispatch('showtoast', {message: $event.detail.content.errors.receipt_number, mode: 'error'});
                        }
                        $dispatch('formerrors', {errors: $event.detail.content.errors});
                    } else{
                        $dispatch('showtoast', {message: $event.detail.content.error, mode: 'error'});
                    }
                }
            "
            @datepicker.window="console.log($event);date = $event.detail.value;"
            @selectmember.window="console.log('ssss');fetchMember($event.detail.id); showform = true; showreceipt = false;"
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
                <div class="my-4">
                    <div class="p-4 border border-base-content border-opacity-20 rounded-md bg-base-200">
                        <div>
                            <h3 class="text-md font-bold my-3 text-center underline text-warning"><span>Receipt Details</span>&nbsp;</h3>
                            <div class="flex flex-row justify-between mt-3 mb-6">
                                <div>
                                    @php
                                        $now = Carbon\Carbon::now();
                                        $today = $now->format('Y-m-d');
                                    @endphp
                                    {{-- <x-inputs.datepicker :element="[
                                        'key' => 'date',
                                        'start_year' => 1980,
                                        'end_year' => \Illuminate\Support\Carbon::now()->year,
                                        'date_format' => 'DD-MM-YYYY',
                                        'authorised' => true,
                                        'label' => 'Date',
                                        'properties' => ['required' => true]
                                    ]"
                                    :_old="['date' => $today]"
                                    :selected_date="$today"
                                    label_position="float"
                                    /> --}}
                                    Date:<span class="text-warning">*</span>
                                    <input type="text" x-model="date" placeholder="(dd-mm-yyyy)" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" class="input input-md" required>
                                    {{-- <input type="text" class="input input-bordered input-sm"> --}}
                                </div>

                                <div>
                                    Book No.:<span class="text-warning">*</span>
                                    <input x-model="bookNo" type="text" class="input input-bordered input-md" required>
                                </div>
                                <div>
                                    Receipt No.:<span class="text-warning">*</span>
                                    <input x-model="receiptNo" type="text" class="input input-bordered input-md" required>
                                </div>
                            </div>
                            <div class="w-full overflow-x-scroll">
                                <table class="table text-sm table-compact mx-auto w-full table-auto ">
                                    <tr>
                                        <td class="text-center">Particulars</td>
                                        {{-- <td class="text-center">Tenure<br>(No. of Months)</td> --}}
                                        <td class="text-center">From<br>(mm-yyyy)</td>
                                        <td class="text-center">To<br>(mm-yyyy)</td>
                                        <td class="text-center">Amount<br>(Rs.)</td>
                                        <td></td>
                                    </tr>
                                    <template x-for="(fee, i) in fees">
                                        <tr>
                                            <td class="w-52">
                                                <select :name="'fee_type_id['+i+']'" class="select select-sm md:select-md min-w-48 select-bordered w-full max-w-xs p-0 md:p-2" x-model="fee.particulars" required>
                                                    <option value="" disabled selected>Select One</option>
                                                    @foreach ($feeTypes as $id => $name)
                                                        <option value="{{$id}}">{{$name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            {{-- <td class="">
                                                <div>
                                                    <input :name="'tenure['+i+']'" class="input input-sm md:input-md input-bordered" type="text" x-model="fee.tenure" disabled
                                                    {{-- :disabled="!typesWithTenure.includes(parseInt(fee.particulars))" --}}
                                                    @input="getFromToMonths(i);">
                                                </div>
                                            </td> --}}
                                            <td class="">
                                                <input :name="'period_from['+i+']'" class="input input-sm md:input-md input-bordered w-full" type="text" x-model="fee.from" :required="typesWithTenure.includes(parseInt(fee.particulars))"
                                                :disabled="!typesWithTenure.includes(parseInt(fee.particulars))" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]"
                                                {{-- @change="onFromChanged(i);" --}}
                                                {{-- @input="formatFromDate(i);" --}}
                                                {{-- @focus="formatFromDate(i);" --}}
                                                placeholder="dd-mm-yyyy"
                                                >
                                            </td>
                                            <td class="">
                                                <input :name="'period_to['+i+']'" class="input input-sm md:input-md input-bordered w-full" type="text" x-model="fee.to" :disabled="!typesWithTenure.includes(parseInt(fee.particulars))" :required="typesWithTenure.includes(parseInt(fee.particulars))"  pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" placeholder="dd-mm-yyyy">
                                            </td>
                                            <td class="">
                                                <input :name="'amount['+i+']'" class="input input-sm md:input-md input-bordered w-full" type="text" x-model="fee.amount" required>
                                            </td>
                                            <td>
                                                <div class="flex flex-row" :class="i != (fees.length - 1) || 'space-x-2'">
                                                    <button type="button" x-show="i == (fees.length - 1)" class="btn btn-sm btn-warning" @click.prevent.stop="addFeeItem();">
                                                        <x-easyadmin::display.icon icon="easyadmin::icons.plus" height="h-4" width="w-4" />
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-error"
                                                        @click.prevent.stop="removeFeeItem(i);" :disabled="fees.length == 1">
                                                        <x-easyadmin::display.icon icon="easyadmin::icons.delete" height="h-4" width="w-4"/>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr>
                                        <td colspan="3" class="text-right">
                                            <span>Total:&nbsp;</span>
                                        </td>
                                        <td>
                                            <input :value="total()" class="input input-md input-bordered w-full" type="text" disabled>
                                        </td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="divider"></div>
                            <div class="flex flex-wrap space-x-0 md:space-x-4 items-end">
                                <textarea x-model="notes" class="my-2 md:my-0 textarea textarea-bordered w-full md:w-3/4" placeholder="Notes"></textarea>
                                <button class="my-2 md:my-0 btn btn-md btn-warning flex-grow"> Create Receipt </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
        <div id="receipt" x-show="showreceipt" class="w-full md:w-10/12 m-auto p-3 border            border-base-content border-opacity-50 rounded-md my-8 min-w-48 overflow-x-scroll">
            <div>
                <div class="text-center my-4 font-bold underline text-warning">Receipt</div>
                <div class="flex flex-row flex-wrap justify-between items-center w-full p-2">
                    <div>
                        <span class="text-warning">Receipt No.:</span>
                        <span x-text="receipt.receipt_number"></span>
                    </div>
                    <div>
                        <span class="text-warning">Date.:</span>
                        <span x-text="receipt.formatted_receipt_date"></span>
                    </div>
                </div>
                <div>
                    <table class="w-full table table-compact">
                        <tbody>
                            <tr class="border-b border-base-content border-opacity-50">
                                <td class="bg-base-200">Particulars</td>
                                <td class="bg-base-200">From</td>
                                <td class="bg-base-200">To</td>
                                <td class="bg-base-200 text-center">Amount</td>
                            </tr>
                            <template x-for="item in receipt.fee_items">
                                <tr>
                                    <td x-text="item.fee_type.name"></td>
                                    <td x-text="item.period_from || '--'"></td>
                                    <td x-text="item.period_to || '--'"></td>
                                    <td class="text-right" x-text="item.amount"></td>
                                </tr>
                            </template>
                        </tbody>
                        <tr class="border-t border-base-content border-opacity-50">
                            <td colspan="3" class="text-right text-warning">Total: </td>
                            <td colspan="1" class="text-right" x-text="receipt.total_amount"></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="bg-base-200"><span class="font-bold text-warning">Notes:&nbsp;</span><span x-text="receipt.notes"></span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="flex flex-row space-x-4 justify-center items-center p-4 mt-4 print:hidden">
                <button @click.prevent.stop="close()" class="btn btn-sm btn-accent">Add Next</button>
            </div>
        </div>
        {{-- <template x-if="member != null"> --}}
        {{-- <div class="my-4">
            <h3 class="text-md font-bold mt-3 mb-1 text-center underline text-warning"><span>Transaction History</span>&nbsp;</h3>
            <template x-if="member.fee_payments.length == 0">
                <div class="text-center text-error">There is no transaction made by this member.</div>
            </template>

            <div x-show="member.fee_payments.length != 0" class="text-xs text-warning text-center mb-4">(Total <span x-text="member.fee_payments.length"></span> Receipts)</div>

            <template x-if="member.fee_payments.length > 0">
            <div class="w-full max-h-64 overflow-x-scroll">
                <table class="table table-compact table-zebra w-full mx-auto">
                    <thead>
                        <tr class="sticky top-0">
                            <td class="sticky top-0">Date</td>
                            <td class="sticky top-0">Receipt No.</td>
                            <td class="sticky top-0">Particulars</td>
                            <td class="sticky top-0">Amount</td>
                            <td class="sticky top-0">From</td>
                            <td class="sticky top-0">To</td>
                            <td class="sticky top-0">Tenure</td>
                            <td class="sticky top-0">Notes</td>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="fp in member.fee_payments">
                            <template x-for="item in fp.fee_items">
                                <tr>
                                    <td><span x-text="fp.receipt_date"></span></td>
                                    <td><span @click.stop.prevent="fetchReceipt(fp.id)" x-text="fp.receipt_number" class="cursor-pointer text-warning"></span></td>
                                    <td><span x-text="item.fee_type.name"></span></td>
                                    <td><span x-text="item.amount"></span></td>
                                    <td><span x-text="typeof item.period_from != 'undefined' ? formatDate(item.period_from) : ''"></span></td>
                                    <td><span x-text="typeof item.period_to != 'undefined' ? formatDate(item.period_to) : ''"></span></td>
                                    <td><span x-text="item.tenure"></span></td>
                                    <td><span x-text="fp.notes"></span></td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
            </template>
        </div> --}}
        {{-- </template> --}}
    </div>
</x-easyadmin::partials.adminpanel>
