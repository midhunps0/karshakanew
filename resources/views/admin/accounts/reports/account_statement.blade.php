<x-easyadmin::partials.adminpanel>
    <div x-data="{
            account_id: '',
            from: '',
            to: '',
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
            doSubmit() {
                let params = {
                    account_id: this.account_id,
                    from: this.from,
                    to: this.to
                };
                $dispatch('linkaction', {link: '{{route('accounts.account.statement')}}', params: params, route: 'accounts.account.statement'});
            }
        }"
        x-init="
            account_id = {{request()->input('account_id', 'null')}};
            from = '{{$from}}';
            to = '{{$to}}';
        "
        >
        <h3 class="text-xl font-bold pb-3"><span>Account Statement</span>&nbsp;</h3>

        <form action="">
            <div class="flex flex-row justify-start space-x-4 mb-4">
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                      <span class="label-text">Account</span>
                    </label>
                    <select x-model="account_id" class="select select-bordered">
                      <option value="">--Select one--</option>
                      @foreach ($allAccounts as $a)
                          <option value="{{$a->id}}">{{$a->name}}</option>
                      @endforeach
                    </select>
                </div>
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                    <span class="label-text">From</span>
                    </label>
                    <input x-model="from" @change="formatDate($el, $event);" type="text" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                </div>
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                    <span class="label-text">To</span>
                    </label>
                    <input x-model="to" @change="formatDate($el, $event);" type="text" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                </div>
            </div>
            <div class="p-2 ">
                <button @click.prevent.stop="doSubmit();" class="btn btn-sm btn-success">
                    Get Statement
                </button>
            </div>
        </form>
        @if ($account != null)
        <div x-show="account_id != ''">
            <h4 class="text-sm font-bold">Account: {{$account->name}}</h4>
            <div class="my-2 font-bold">
                Period: <span x-text="from"></span> To <span x-text="to"></span>
            </div>
            <div class="rounded-md border border-base-200 w-3/4">
                <table class="table table-compact w-full">
                    <thead>
                        <tr>
                            <td>Date</td>
                            <td>Particulars</td>
                            <td class="text-center">Debit Rs.</td>
                            <td class="text-center">Credit Rs.</td>
                            {{-- <td>Bal. Rs.</td> --}}
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="font-bold border-b border-base-200">
                            <td>{{$statement['opening']['date']}}</td>
                            <td>{{$statement['opening']['description']}}</td>
                            <td class="text-right px-4">
                                @if($statement['opening']['account_action'] == 'debit')
                                {{$statement['opening']['amount']}}
                                @endif
                            </td>
                            <td class="text-right px-4">
                                @if($statement['opening']['account_action'] == 'credit')
                                {{$statement['opening']['amount']}}
                                @endif
                            </td>
                            {{-- <td class="text-right">{{$statement['opening']['net_balance']}}</td> --}}
                        </tr>
                    </tbody>
                    <tbody>
                    @if (isset($statement['transactions']))
                        @foreach ($statement['transactions'] as $t)
                            @foreach ($t['opposite_clients'] as $oc)
                            <tr @if ($loop->last)
                                class="border-b border-base-200"
                            @endif>
                                <td>
                                    @if ($loop->first)
                                    {{$t['date']}}
                                    @endif
                                </td>
                                <td>
                                    {{$oc['description']}}
                                </td>
                                <td class="text-right px-4">
                                    @if ($oc['account_action'] == 'debit')
                                    {{$oc['amount']}}
                                    @endif
                                </td>
                                <td class="text-right px-4">
                                    @if ($oc['account_action'] == 'credit')
                                    {{$oc['amount']}}
                                    @endif
                                </td>
                                {{-- <td>
                                    {{$oc['net_balance']}}
                                </td> --}}
                            </tr>
                            @endforeach
                        @endforeach
                    @endif
                    </tbody>
                    <tbody>
                        <tr class="font-bold">
                            <td>{{$statement['closing']['date']}}</td>
                            <td>{{$statement['closing']['description']}}</td>
                            <td class="text-right px-4">
                                @if($statement['closing']['account_action'] == 'debit')
                                {{$statement['closing']['amount']}}
                                @endif
                            </td>
                            <td class="text-right px-4">
                                @if($statement['closing']['account_action'] == 'credit')
                                {{$statement['closing']['amount']}}
                                @endif
                            </td>
                            {{-- <td class="text-right">{{$statement['closing']['net_balance']}}</td> --}}
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-easyadmin::partials.adminpanel>
