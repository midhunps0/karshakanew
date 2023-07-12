<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Journal Entries</span>&nbsp;</h3>
        <div>
            <form
                x-data="{
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
                            from: this.from,
                            to: this.to
                        };
                        $dispatch('linkaction', {link: '{{route('transaction.index')}}', params: params, route: 'transaction.index'});
                    }
                }"
                x-init="
                    from = '{{request()->get('from') ?? ''}}';
                    to = '{{request()->get('to') ?? ''}}';
                "
                action=""
                class="mb-8"
                >
                <div class="flex flex-row justify-evenly mb-4">
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
                <div class="text-center">
                    <button type="submit" class="btn btn-sm btn-warning"
                    @click.prevent.stop="doSubmit();">Get Journal Entries</button>
                </div>
            </form>
            <div>
                <table class="table table-compact w-full">
                    <thead>
                        <tr>
                            <td class="w-24"></td>
                            <td class="w-20"></td>
                            <td class="w-48"></td>
                            <td colspan="2" class="text-center text-error opacity-75">Debit</td>
                            <td colspan="2" class="text-center text-success opacity-95">Credit</td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td>Instr.<br/>No.</td>
                            <td>Receipt/<br/>Voucher No.</td>
                            <td class="text-error opacity-75">Account</td>
                            <td class="text-error opacity-75 w-24">Amount</td>
                            <td class="text-success opacity-95">Account</td>
                            <td class="text-success opacity-95 w-24">Amount</td>
                        </tr>
                    </thead>
                    {{-- {{dd($transactions)}} --}}
                    <tbody>
                        @foreach ($transactions as $t)
                            @foreach ($t->clients as $c)
                            <tr>
                                <td class="border-l border-r border-r-base-content border-l-base-content border-opacity-10 @if($loop->last) border-b border-b-base-content @endif">
                                    @if ($loop->first)
                                        {{$t->date}}
                                    @endif
                                </td>
                                <td class="border-l border-r border-r-base-content border-l-base-content border-opacity-10 @if($loop->last) border-b border-b-base-content @endif">
                                    @if ($loop->first)
                                        {{$t->instrument_no}}
                                    @endif
                                </td>
                                <td class="border-l border-r border-r-base-content border-l-base-content border-opacity-10 @if($loop->last) border-b border-b-base-content @endif w-40">
                                    @if ($loop->first)
                                        {{$t->receipt_voucher_no}}
                                    @endif
                                </td>
                                @if ($c->action == 'debit')
                                    <td class="border-r border-r-base-content border-opacity-10 @if($loop->last) border-b border-b-base-content @endif">{{$c->ledgerAccount->name}}</td>
                                    <td class="border-r border-r-base-content border-opacity-10 @if($loop->last) border-b border-b-base-content @endif text-right">{{$c->client_amount}}</td>
                                @else
                                    <td class="border-r border-r-base-content border-opacity-10 @if($loop->last) border-b border-b-base-content @endif"></td>
                                    <td class="border-r border-r-base-content border-opacity-10 @if($loop->last) border-b border-b-base-content @endif"></td>
                                @endif
                                @if ($c->action == 'credit')
                                    <td class="border-r border-r-base-content border-opacity-10 @if($loop->last) border-b border-b-base-content @endif">{{$c->ledgerAccount->name}}</td>
                                    <td class="border-r border-r-base-content border-opacity-10 @if($loop->last) border-b border-b-base-content @endif text-right">{{$c->client_amount}}</td>
                                @else
                                <td class="border-r border-r-base-content border-opacity-10 @if($loop->last) border-b border-b-base-content @endif"></td>
                                <td class="border-r border-r-base-content border-opacity-10 @if($loop->last) border-b border-b-base-content @endif"></td>
                                @endif
                            </tr>
                            @endforeach
                        @endforeach





                            {{-- <tr>
                                <td>{{$t->date}}</td>
                                <td>
                                @foreach ($t->clients as $c)
                                    <tr>
                                        @if ($c->action == 'debit')
                                        <td>{{$c->ledgerAccount->name}}</td>
                                        <td>{{$c->client_amount}}</td>
                                        @endif
                                        @if (true)
                                        <td></td>
                                        <td></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                                </td>
                            </tr> --}}

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>
