<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Create Receipt</span>&nbsp;</h3>
        <div class="text-right px-4">
            <a href="" class="btn btn-sm" @click.prevent.stop="history.back();" >Back</a>
        </div>
        <div x-data="{
                url: '{{route('transaction.store')}}',
                date: '',
                type: 'receipt',
                remarks: '',
                ref_no: '',
                instrument_no: '',
                debit_total: 0,
                credit_total: 0,
                errors: '',
                chosen_accounts: {
                    debits: [],
                    credits: []
                },
                debit_clients: [
                    {
                        account_id: null,
                        amount: 0,
                        action: 'debit',
                    },
                ],
                credit_clients: [
                    {
                        account_id: null,
                        amount: 0,
                        action: 'credit',
                    },
                ],
                addClient(type) {
                    switch(type) {
                        case 'debit':
                            this.debit_clients.push(
                                {
                                    account_id: null,
                                    amount: 0,
                                    action: 'debit',
                                });
                            break;
                        case 'credit':
                            this.credit_clients.push(
                                {
                                    account_id: null,
                                    amount: 0,
                                    action: 'credit',
                                });
                            break;
                    }
                },
                removeClient(type, i) {
                    switch(type) {
                        case 'debit':
                            this.debit_clients = this.debit_clients.filter(
                                (dc, index) => {
                                    return index != i;
                                }
                            );
                            break;
                        case 'credit':
                            this.credit_clients = this.credit_clients.filter(
                                    (dc, index) => {
                                        return index != i;
                                    }
                                );
                            break;
                    }
                },
                validate(type, index) {
                    let item = null;
                    switch(type) {
                        case 'debit':
                            item = this.debit_clients.filter((d, i) => {
                                return index == i;
                            })[0];
                            break;
                        case 'credit':
                            item = this.credit_clients.filter((d, i) => {
                                return index == i;
                            })[0];
                            break;
                    }
                    let regx = /[0-9]+/g;
                    let matchArr = (item.amount+'').match(regx);
                    item.amount = matchArr != null ? (matchArr.join() * 1) + '' : 0;
                },
                formatDate(el, event) {
                    let re = /[0-9,-]/g;
                    let x = (el.value.match(re) || []).join('');
                    let arr = x.split('-');
                    let newarr = [];
                    for(i = 0; i < arr.length; i++) {
                        if (i < 2) {
                            newarr.push(arr[i].padStart(2, '0'));
                        } else {
                            newarr.push(arr[i]);
                        }
                    }
                    {{-- el.value = newarr.join('-'); --}}
                },
                resetForm() {
                    this.remarks = '';
                    this.ref_no = '';
                    this.debit_total = 0;
                    this.credit_total = 0;
                    this.errors = '';
                    this.chosen_accounts == {
                        debits: [],
                        credits: []
                    };
                    this.debit_clients = [
                        {
                            account_id: null,
                            amount: 0,
                            action: 'debit',
                        },
                    ];
                    this.credit_clients = [
                        {
                            account_id: null,
                            amount: 0,
                            action: 'credit',
                        },
                    ];
                },
                doSubmit() {
                    let hasErrors = false;
                    let deArr = [];
                    this.debit_clients.forEach((d) => {
                        if (d.account_id == null || d.account_id == '' && deArr.length == 0) {
                            deArr.push('Account not chosen for some debit entries.');
                            hasErrors = true;
                        }
                    });
                    let ceArr = [];
                    this.credit_clients.forEach((c) => {
                        if (c.account_id == null || c.account_id == '' && ceArr.length == 0) {
                            ceArr.push('Account not chosen for some credit entries.');
                            hasErrors = true;
                        }
                    });
                    let glue = '';
                    if (deArr.length > 0 && ceArr.length > 0) {
                        glue = '<br/>';
                    }
                    this.errors = (deArr.join('. ') + glue + ceArr.join('. ')).trim();
                    if (this.errors.length > 0) {
                        glue = '<br/>';
                    } else {
                        glue = '';
                    }
                    if (this.debit_total != this.credit_total) {
                        this.errors += glue + 'Debit and Credit totals mismatch.';
                        glue = '<br/>';
                        hasErrors = true;
                    }
                    if (this.date == '') {
                        this.errors += glue + 'The date field is required.';
                        glue = '<br/>';
                        hasErrors = true;
                    }
                    if (!hasErrors) {
                        let formData = new FormData();
                        formData.append('date', this.date);
                        formData.append('type', this.type);
                        formData.append('remarks', this.remarks);
                        formData.append('ref_no', this.ref_no);
                        formData.append('instrument_no', this.instrument_no);
                        let clients = [...this.debit_clients, ...this.credit_clients];
                        console.log(clients);
                        clients.forEach((c, i) => {
                            formData.append(
                                `clients[${i}][account_id]`, c.account_id
                            );
                            formData.append(
                                `clients[${i}][amount]`, c.amount
                            );
                            formData.append(
                                `clients[${i}][action]`, c.action
                            );
                        });
                        $dispatch('formsubmit', { url: this.url, formData: formData, target: 'create_journal_form' });
                    }
                }
            }"
            x-init="
                $watch('debit_clients', (val) => {
                    chosen_accounts.debits = [];
                    debit_total = val.reduce((r, item) => {
                        chosen_accounts.debits.push(item.account_id * 1);
                        return (item.amount * 1) + r;
                    }, 0);
                    errors = '';
                });
                $watch('credit_clients', (val) => {
                    chosen_accounts.credits = [];
                    credit_total = val.reduce((r, item) => {
                        chosen_accounts.credits.push(item.account_id * 1);
                        return (item.amount * 1) + r ;
                    }, 0);
                    errors = '';
                });
                let d = new Date();
                date = d.getDate() + '-' + String(d.getMonth()+1).padStart(2,'0')+ '-' + d.getFullYear();
                {{-- date = '12-01-2022'; --}}
            "
            @formresponse.window="
                if ($event.detail.target == $el.id) {
                    console.log('form response captured');
                    if ($event.detail.content.success) {
                        $dispatch('showtoast', {message: 'Receipt Created', mode: 'success', });
                        resetForm();
                    }
                }

            "
            class="p-4"
            id="create_journal_form"
            >
            <form action="">
                <div class="flex flex-row justify-between mb-4">
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                        <span class="label-text">Date</span>
                        </label>
                        <input x-model="date" @change="formatDate($el, $event);" type="text" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                        <span class="label-text">Instrument No.:</span>
                        </label>
                        <input x-model="instrument_no" type="text" class="input input-bordered w-full max-w-xs" required/>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                        <span class="label-text">Ref. No.:</span>
                        </label>
                        <input x-model="ref_no" type="text" class="input input-bordered w-full max-w-xs" required/>
                    </div>
                </div>
                <div class="flex flex-row space-x-4">
                    <div class="w-1/2">
                        <h4 class="font-bold text-warning mb-2">Debit</h4>
                        <div class="border border-base-200 rounded-2xl">
                            <table class="table table-compact w-full">
                                <thead>
                                    <th>Account</th>
                                    <th colspan="2">Amount</th>
                                </thead>
                                <tbody>
                                    <template x-for="(dc, index) in debit_clients" :key="index">
                                        <tr>
                                            <td>
                                                <div x-data="{
                                                        account_id: '',
                                                        showlist: false,
                                                        accounts: [],
                                                        txt: '',
                                                        filteredAccounts: [],
                                                        setAccount(id) {
                                                            $refs['idbox'].value = id;
                                                            this.txt = '';
                                                            this.txt = this.accounts.filter((a) => {
                                                                return a.id == id;
                                                            })[0].name;
                                                            this.showlist = false;
                                                        }
                                                    }"
                                                    x-init="
                                                        @foreach ($cashOrBank as $a)
                                                            accounts.push(
                                                                {
                                                                    id: {{$a->id}},
                                                                    name: '{{$a->name}}',
                                                                }
                                                            );
                                                        @endforeach
                                                        console.log('accounts');
                                                        console.log(accounts);
                                                        $watch(
                                                            'txt',
                                                            (val) => {
                                                                filteredAccounts = accounts.filter(
                                                                    (a) => {
                                                                        let x = val.length;
                                                                        return a.name.substring(0, x).toLowerCase() == val.toLowerCase();
                                                                    }
                                                                );
                                                            }
                                                        );
                                                    ">
                                                    <input x-ref="idbox" type="hidden" x-model="dc.account_id">
                                                    <input @keyup.prevent.stop="txt.length > 0 ? showlist = true : showlist = false;" x-model="txt" type="text" class="input input-sm input-bordered w-full">
                                                    <div x-show="txt.length > 0 && showlist" class="border border-base-content border-opacity-20 rounded-md shadow-md max-h-60 overflow-y-scroll bg-base-200 absolute top-15 z-10">
                                                        <template x-for="a in filteredAccounts">
                                                            <div>
                                                                <button @click.prevent.stop="setAccount(a.id);" class="p-2 hover:bg-base-300 w-full text-left border-b border-base-content border-opacity-5" x-text="a.name"></button>
                                                            </div>
                                                        </template>
                                                </div>
                                                {{-- <select x-model="dc.account_id" type="select" class="select select-sm min-w-72 py-0 select-bordered rounded">
                                                    <option value="">--Select--</option>
                                                    @foreach ($cashOrBank as $a)
                                                        <option value="{{$a->id}}" :disabled="chosen_accounts.debits.includes({{$a->id}}) || chosen_accounts.credits.includes({{$a->id}})">{{$a->name}}</option>
                                                    @endforeach
                                                </select> --}}
                                            </td>
                                            <td>
                                                <input type="text" x-model="dc.amount" class="input input-sm input-bordered rounded" @keyup="validate('debit', index)">
                                            </td>
                                            <td>
                                                {{-- <button type="button" x-show="index == (debit_clients.length - 1) && credit_clients.length == 1" class="btn btn-sm btn-warning"
                                                @click.prevent.stop="addClient('debit')">
                                                    <x-easyadmin::display.icon
                                                    icon="easyadmin::icons.plus"
                                                    height="h-4" width="w-4"/>
                                                </button>
                                                <button type="button" x-show="debit_clients.length > 1"
                                                @click.prevent.stop="removeClient('debit', index)"
                                                class="btn btn-sm btn-error">
                                                    <x-easyadmin::display.icon
                                                    icon="easyadmin::icons.delete"
                                                    height="h-4" width="w-4"/>
                                                </button> --}}
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td class="bg-base-200">Total</td>
                                        <td class="bg-base-200">
                                            <input class="input input-sm" type="text" readonly x-model="debit_total">
                                        </td>
                                        <td class="bg-base-200"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="w-1/2">
                        <h4 class="font-bold text-warning mb-2">Credit</h4>
                        <div class="border border-base-200 rounded-2xl">
                            <table class="table table-compact w-full">
                                <thead>
                                    <th>Account</th>
                                    <th colspan="2">Amount</th>
                                </thead>
                                <tbody>
                                    <template x-for="(cc, index) in credit_clients" :key="index">
                                        <tr>
                                            <td>
                                                <div x-data="{
                                                        account_id: '',
                                                        showlist: false,
                                                        accounts: [],
                                                        txt: '',
                                                        filteredAccounts: [],
                                                        setAccount(id) {
                                                            $refs['idbox'].value = id;
                                                            this.txt = '';
                                                            this.txt = this.accounts.filter((a) => {
                                                                return a.id == id;
                                                            })[0].name;
                                                            this.showlist = false;
                                                        }
                                                    }"
                                                    x-init="
                                                        @foreach ($accounts as $a)
                                                            accounts.push(
                                                                {
                                                                    id: {{$a->id}},
                                                                    name: '{{$a->name}}',
                                                                }
                                                            );
                                                        @endforeach
                                                        console.log('accounts');
                                                        console.log(accounts);
                                                        $watch(
                                                            'txt',
                                                            (val) => {
                                                                filteredAccounts = accounts.filter(
                                                                    (a) => {
                                                                        let x = val.length;
                                                                        return a.name.substring(0, x).toLowerCase() == val.toLowerCase();
                                                                    }
                                                                );
                                                            }
                                                        );
                                                    ">
                                                    <input x-ref="idbox" type="hidden" x-model="cc.account_id">
                                                    <input @keyup.prevent.stop="txt.length > 0 ? showlist = true : showlist = false;" x-model="txt" type="text" class="input input-sm input-bordered w-full">
                                                    <div x-show="txt.length > 0 && showlist" class="border border-base-content border-opacity-20 rounded-md shadow-md max-h-60 overflow-y-scroll bg-base-200 absolute top-15 z-10">
                                                        <template x-for="a in filteredAccounts">
                                                            <div>
                                                                <button @click.prevent.stop="setAccount(a.id);" class="p-2 hover:bg-base-300 w-full text-left border-b border-base-content border-opacity-5" x-text="a.name"></button>
                                                            </div>
                                                        </template>
                                                </div>
                                                {{-- <select x-model="cc.account_id" type="select" class="select select-sm min-w-72 py-0 select-bordered rounded">
                                                    <option value="">--Select--</option>
                                                    @foreach ($accounts as $a)
                                                        <option value="{{$a->id}}" :disabled="chosen_accounts.debits.includes({{$a->id}}) || chosen_accounts.credits.includes({{$a->id}})">{{$a->name}}</option>
                                                    @endforeach
                                                </select> --}}
                                            </td>
                                            <td>
                                                <input type="text" x-model="cc.amount" class="input input-sm input-bordered rounded" @keyup="validate('credit', index)">
                                            </td>
                                            <td>
                                                <button type="button" x-show="index == (credit_clients.length - 1) && debit_clients.length == 1" class="btn btn-sm btn-warning"
                                                @click.prevent.stop="addClient('credit')">
                                                    <x-easyadmin::display.icon
                                                    icon="easyadmin::icons.plus"
                                                    height="h-4" width="w-4"/>
                                                </button>
                                                <button type="button" x-show="credit_clients.length > 1"
                                                @click.prevent.stop="removeClient('credit', index)"
                                                class="btn btn-sm btn-error">
                                                    <x-easyadmin::display.icon
                                                    icon="easyadmin::icons.delete"
                                                    height="h-4" width="w-4"/>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td class="bg-base-200">Total</td>
                                        <td class="bg-base-200">
                                            <input class="input input-sm" type="text" readonly x-model="credit_total">
                                        </td>
                                        <td class="bg-base-200"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-center items-center">
                    <div class="form-control w-full">
                        <label class="label">
                            Remarks
                        </label>
                        <textarea x-model="remarks" class="w-full textarea textarea-bordered textarea-sm h-8" rows="3"></textarea>
                    </div>
                </div>
                <div class="flex flex-row justify-center items-center my-8">
                    <button type="button" class="btn btn-sm btn-success" @click.prevent.stop="doSubmit">
                        Create Receipt
                    </button>
                </div>
                <div x-show="errors != ''" class="flex flex-row justify-center items-center">
                    <div class="text-center bg-error bg-opacity-70 p-4 rounded border border-base-content border-opacity-20" x-html="errors"></div>
                </div>
            </form>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>
