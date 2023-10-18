<x-easyadmin::partials.adminpanel>
    <h3 class="text-xl font-bold pb-3"><span>Create New Receipt</span>&nbsp;</h3>
    @php
        $feeTypes = \App\Models\FeeType::all()->pluck('name', 'id');
        $typesWithTenure = config('generalSettings.fee_types_with_tenure');
        $memberId = request()->input('m', null);
    @endphp
    <div class="w-full"
        x-data="{
            {{-- member: null, --}}
            members: [],
            receipts: [],
            showReceipts: false,
            date: null,
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
            duplicateMember: false,
            formok() {
                let ok = true;
                this.fees.forEach((f) => {
                    if (f.particulars == '' || f.amount == null || f.amount == 0) {
                        ok = false;
                    }
                });
                return ok;
            },
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
            resetFees() {
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
            },
            fetchMember(id) {
                console.log('id: '+id)
                axios.get(
                    '{{route('members.fetch', '_X_')}}'.replace('_X_', id)
                ).then((r) => {
                    let member = r.data.member;
                    console.log(member);
                    let x = this.members.filter((m) => {
                        return m.id == member.id
                    });
                    if (x.length == 0) {
                        this.members.push(
                            {
                                id: member.id,
                                membership_no: member.membership_no,
                                aadhaar_no: member.aadhaar_no,
                                name: member.name,
                                name_mal: member.name_mal,
                                taluk: member.taluk.name,
                                village: member.village.name
                            }
                        );
                    } else {
                        this.duplicateMember = true;
                    }
                    console.log(this.members);
                }).catch((e) => {
                    console.log(e);
                });
                this.receipts = [];
            },
            removeMember(id) {
                this.members = this.members.filter((m) => {
                    return m.id != id;
                });
            },
            fetchReceipt(id = null) {
                if (id == null) {
                    id = this.receipt.id
                }
                axios.get(
                    '{{route('receipt.fetch', '_X_')}}'.replace('_X_', id)
                ).then((r) => {
                    this.receipt = r.data.receipt;
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

                f.history = false;
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
                let divContents = document.getElementById('bulk-receipts-div').innerHTML;
                let a = window.open('', '', 'height=350, width=500');
                a.document.write('<html>');
                a.document.write('<body>');
                a.document.write(divContents);
                a.document.write('</body></html>');
                a.document.close();
                a.print();
            },
            newReceipt() {
                this.member = null;
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
            },
            close() {
                $dispatch('linkaction', {link: '{{route('feecollections.create')}}', route: 'feecollections.create'});
            },
            doSubmit() {
                {{-- let form = document.getElementById('{{$form['id']}}'); --}}
                let fd = new FormData();
                {{-- let date = Date.parse(this.date); --}}
                fd.append('members', this.members.map((m) => {
                    return m.id
                }).join(','));
                fd.append('date', this.date);
                this.fees.forEach((f, i) => {
                    fd.append('fee_item['+i+'][fee_type_id]', f.particulars);
                    if (f.tenure != null && f.tenure != '') {
                        fd.append('fee_item['+i+'][period_from]', f.from);
                        fd.append('fee_item['+i+'][period_to]', f.to);
                    }
                    fd.append('fee_item['+i+'][amount]', f.amount);
                });
                if (this.notes != '') {fd.append('notes', this.notes);}
                this.showReceipts = true;
                {{-- fd.append('x_fr', 'form_user_create'); --}}
                {{-- fd.append('member_id', this.member.id); --}}
                $dispatch('formsubmit', { url: this.postUrl, formData: fd, target: '{{$form['id']}}', fragment: 'create_form' });
            }
        }"
        x-init=""
        >
        <form
                @formresponse.window="console.log($event.detail);
                console.log('fr captured');
                if ($event.detail.target == $el.id) {
                    if ($event.detail.content.success) {
                        $event.detail.content.receipts.forEach((r) => {
                            receipts.push(r);
                        });
                        members = [];
                        {{-- $dispatch('shownotice', {message: 'Receipt Created', mode: 'success', }); --}}
                        console.log('receipts');
                        console.log(receipts);
                        $dispatch('formerrors', {errors: []});
                    } else if (typeof $event.detail.content.errors != undefined) {
                        $dispatch('formerrors', {errors: $event.detail.content.errors});
                    } else{
                        $dispatch('shownotice', {message: $event.detail.content.error, mode: 'error', redirectUrl: null, redirectRoute: null});
                    }
                }
            "
            @datepicker.window="date = $event.detail.value;"
            @selectmember.window="fetchMember($event.detail.id);"
            class="p-1" action=""
            id="{{$form['id']}}"
            x-init="
            postUrl='{{route('members.fees.store_bulk')}}'
            @foreach ($typesWithTenure as $t)
                typesWithTenure.push(parseInt({{$t}}));
            @endforeach
            console.log(typesWithTenure);
            "
            >

            <div class="">
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
                                    <x-inputs.datepicker :element="[
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
                                    />
                                    {{-- <input type="text" class="input input-bordered input-sm"> --}}
                                </div>
                                {{-- <div>
                                    Receipt No.:
                                    <input type="text" class="input input-bordered input-sm">
                                </div> --}}
                            </div>
                            <div class="w-full overflow-x-scroll relative">
                                <table class="table text-sm table-compact mx-auto w-full table-auto ">
                                    <tr>
                                        <td class="text-center">Particulars</td>
                                        <td class="text-center">Tenure<br>(No. of Months)</td>
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
                                            <td class="">
                                                <div>
                                                    <input :name="'tenure['+i+']'" class="input input-sm md:input-md input-bordered" type="text" x-model="fee.tenure" :disabled="!typesWithTenure.includes(parseInt(fee.particulars))"
                                                    @input="getFromToMonths(i);">
                                                </div>
                                            </td>
                                            <td class="">
                                                <input :name="'period_from['+i+']'" class="input input-sm md:input-md input-bordered" type="text" x-model="fee.from" :required="typesWithTenure.includes(parseInt(fee.particulars))" :disabled="fee.history" required pattern="[0-3][0-9]-[0-1][0-9]-[0-2][0-9][0-9][0-9]" @change="onFromChanged(i);" @input="formatFromDate(i);" @focus="formatFromDate(i);">
                                            </td>
                                            <td class="">
                                                <input :name="'period_to['+i+']'" class="input input-sm md:input-md input-bordered" type="text" x-model="fee.to" disabled :required="typesWithTenure.includes(parseInt(fee.particulars))"  pattern="[0-3][0-9]-[0-1][0-9]-[0-2][0-9][0-9][0-9]">
                                            </td>
                                            <td class="">
                                                <input :name="'amount['+i+']'" class="input input-sm md:input-md input-bordered" type="text" x-model="fee.amount" required>
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
                                        <td colspan=4 class="text-right">
                                            <span>Total:&nbsp;</span>
                                        </td>
                                        <td>
                                            <input :value="total()" class="input input-md input-bordered" type="text" disabled>
                                        </td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="divider"></div>
                            <div class="flex flex-wrap space-x-0 md:space-x-4 items-end">
                                <textarea x-model="notes" class="my-2 md:my-0 textarea textarea-bordered w-full" placeholder="Notes"></textarea>
                            </div>
                            <div class="divider"></div>
                            <div class="flex flex-row justify-evenly p-2 border border-base-content border-opacity-20 text-warning font-bold rounded-md mb-8 bg-base-100">
                                <div>
                                    <span>No. Of Receipts:</span>&nbsp;
                                    <span x-text="members.length"></span>
                                </div>
                                <div>
                                    <span>Total Amount (Rs.):</span>&nbsp;
                                    <span x-text="total() * members.length"></span>
                                </div>
                            </div>
                            <span class="font-bold text-warning">Add Members:</span>
                            <div>
                                <x-utils.member-selector />
                            </div>
                            <div class="flex flex-row justify-center my-8">
                                <div x-show="members.length > 0" class="border border-base-content border-opacity-20 w-full">
                                    <table class="table table-compact m-auto w-full">
                                        <thead>
                                            <tr>
                                                <th>Membership No.</th>
                                                <th>Name</th>
                                                <th>Aadhaar No.</th>
                                                <th>Taluk</th>
                                                <th>Village</th>
                                                <td></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="m in members">
                                            <tr>
                                                <td><span x-text="m.membership_no"></span></td>
                                                <td>
                                                    <span x-text="m.name"></span>
                                                    <span x-show="m.name != null && m.name.length > 0">/</span>
                                                    <span x-text="m.name_mal"></span>
                                                </td>
                                                <td><span x-text="m.aadhaar_no"></span></td>
                                                <td><span x-text="m.taluk"></span></td>
                                                <td><span x-text="m.village"></span></td>
                                                <td>
                                                    <button type="button" class="text-error" @click.prevent.stop="removeMember(m.id);">
                                                        <x-easyadmin::display.icon icon="easyadmin::icons.close" width="w-4" height="h-4" />
                                                    </button>
                                                </td>
                                            </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="text-center">
                                <button :disabled="!formok()" @click.prevent.stop="doSubmit()" class="my-2 md:my-0 btn btn-md btn-warning flex-grow"> Create Receipts </button><br>
                                <span x-show="!formok()" class="text-error text-xs text-opacity-70">Incomplete Form</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
        <div x-show="showReceipts" class="fixed top-0 left-0 z-50 h-full w-full overflow-x-scroll md:py-4 bg-base-100 bg-opacity-30">
            <div id="bulk-receipts-div" class="w-80 ml-auto mr-auto bg-base-200 p-4 overflow-y-scroll max-h-192">
                <template x-for="r in receipts">
                    <div class="border border-x-base-content border-opacity-20 rounded-md shadow-md p-2">
                        <h3 class="text-lg text-center">
                            Kerala Karshakathozhilali Welfare Board
                        </h3>
                        <h3 class="text-lg text-center underline my-4 !bg-transparent">
                            Receipt
                        </h3>
                        <div>No.: <span x-text="r.receipt_number"></span></div>
                        <div>Date: <span x-text="r.formatted_receipt_date"></span></div>
                        <div>Member: <span x-text="r.member.name != null ? r.member.name : r.member.name_mal"></span></div>
                        <div>Reg. No.: <span x-text="r.member.membership_no"></span></div>
                        <div>
                            <table class="table table-compact w-full">
                                <thead>
                                    <tr>
                                        <th>Particulars</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="item in r.fee_items">
                                        <tr>
                                            <td>
                                                <span x-text="item.fee_type.name"></span><br>
                                                <span x-text="item.period_from != null ? item.period_from + ' to ' + item.period_to : ''"></span>
                                            </td>
                                            <td class="text-right" x-text="item.amount"></td>
                                        </tr>
                                    </template>
                                    <tr>
                                        <td class="font-bold">Total</td>
                                        <td class="text-right font-bold">
                                            <span x-text="r.total_amount"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div><span class="font-bold">Notes:</span>&nbsp;<span x-text="r.notes || ''"></span></div>
                        </div>
                    </div>
                </template>
                <div class="flex flex-row justify-evenly my-4 print:hidden">
                    <button @click.prevent.stop="showReceipts = false;" class="btn btn-sm btn-error bg-opacity-60 m-2 p-0 px-2">
                        Close&nbsp;<x-easyadmin::display.icon icon="easyadmin::icons.close" height="h-6" width="w-6"/>
                    </button>
                    {{-- <button @click.prevent.stop="window.print()" class="btn btn-sm btn-success bg-opacity-60 m-2 p-0 px-2"> --}}
                    <button @click.prevent.stop="printReceipt()" class="btn btn-sm btn-success bg-opacity-60 m-2 p-0 px-2">
                        Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>
