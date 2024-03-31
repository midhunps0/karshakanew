<x-easyadmin::partials.adminpanel>
    <div x-data="{
        dateType: 'receipt_date',
        start: '',
        end: '',
        createdBy: null,
        page: 1,
        dowloadUrl: '',
        getParams() {
            let p = {
                datetype: this.dateType
            };
            if (this.start != '') {
                p['start'] = this.start;
            }
            if (this.end != '') {
                p['end'] = this.end;
            }
            if (this.page != 1) {
                p['page'] = this.page;
            }
            if (this.createdBy != null) {
                p['created_by'] = this.createdBy;
            }
            return p;
        },
        fetchReport() {
            $dispatch('linkaction', {link: '{{route('feecollections.report')}}', route: 'feecollections.report', params: this.getParams(), fresh: true})
        },
        fetchPrintReport() {
            $dispatch('linkaction', {link: '{{route('feecollections.fullreport')}}', route: 'feecollections.report', params: this.getParams(), fresh: true, target: 'feecollection_report', history: false})
        },
        initPrint(data) {
            $dispatch('showreceiptsprint', {
                aggregates: data.aggregates,
                receipts: data.receipts,
                from: this.start,
                to: this.end
            });
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
        }
    }"
    @pageaction="page = $event.detail.page; fetchReport();"
    x-init="
        downloadUrl = '{{route('feecollections.report.download').'?'}}';
        @if (request()->get('datetype') != null)
            dateType = '{{request()->get('datetype')}}';
            downloadUrl += 'datetype=' + dateType + '&';
        @endif
        @if (request()->get('created_by') != null)
            createdBy = '{{request()->get('created_by')}}';
            downloadUrl += 'created_by=' + createdBy + '&';
        @endif
        start = '{{request()->get('start') ?? ''}}';
        downloadUrl += 'start=' + start + '&';
        end = '{{request()->get('end') ?? ''}}';
        downloadUrl += 'end=' + end + '&';
        @if (request()->get('page') != null)
            page = {{request()->get('page')}};
            downloadUrl += 'page=' + page + '&';
        @endif
    "
    @contentupdate.window="
            if($event.detail.target == 'feecollection_report') {
                initPrint($event.detail.content);
            }
        "
    >
        <h3 class="text-xl font-bold pb-3">Fee Collections Report</h3>
        <div>
            <form action="" @submit.prevent.stop="page=1; fetchReport();">
                <div class="flex flex-row space-x-4 items-end justify-start my-4 w-full">
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                          <span class="label-text">Date Type</span>
                        </label>
                        <select class="select select-bordered flex-grow" x-model="dateType">
                            <option value="created_at">Creation Date</option>
                            <option value="receipt_date">Receipt Date</option>
                        </select>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                          <span class="label-text">From</span>
                        </label>
                        <input x-model="start" @change="formatDate($el, $event);" type="text" name="start" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                          <span class="label-text">To</span>
                        </label>
                        <input x-model="end" @change="formatDate($el, $event);" type="text" name="end" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                    </div>
                </div>
                @if ($user->hasPermissionTo('User: View In Any District') ||
                    $user->hasPermissionTo('User: View In Own District'))
                <div class="flex flex-row w-full space-x-4 justify-start items-end my-4">
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                          <span class="label-text">Created By</span>
                        </label>
                        <select class="select select-bordered flex-grow" x-model="createdBy">
                            <option value="">Any</option>
                            @foreach ($appUsers as $u)
                            <option value="{{$u->id}}">{{$u->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="flex flex-row w-full space-x-4 justify-start items-end my-4">
                    <div class="form-control w-full max-w-xs">
                        <button type="submit" class="btn btn-md btn-success">Get Report</button>
                    </div>
                    @if(count($receipts) > 0)
                    <div class="form-control w-full max-w-xs">
                        <button @click.prevent.stop="fetchPrintReport();" type="button" class="btn btn-md btn-warning">Print View</button>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <a :href="downloadUrl" class="btn btn-md btn-warning" download>Download</a>
                    </div>
                    @endif
                    {{-- @if(count($receipts) > 0)
                    <div class="form-control w-full max-w-xs">
                        <a href="{{request()->url()}}" class="btn btn-md btn-warning">Download</button>
                    </div>
                    @endif --}}
                </div>
            </form>
            @if (count($aggregates) > 0)
            <div class="flex justify-center my-12">
                <div class="border border-base-content border-opacity-20 rounded-xl">
                <h2 class="font-bold m-3">Fee Collection Summary:</h2>
                    <table class="table table-compact">
                        <thead>
                            <tr>
                                <th>Fee Type</th>
                                <th class="text-right">Total Count</th>
                                <th class="text-right">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($aggregates as $a => $v)
                            <tr @if ($a == 'Total')
                                class="font-bold"
                            @endif>
                                <td>{{$a}}</td>
                                <td class="text-right">{{$v->c_count}}</td>
                                <td class="text-right">{{$v->c_sum}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @if(count($receipts) > 0)
            <div>
                <div class="mx-auto border border-base-content border-opacity-10 rounded-lg h-96 overflow-scroll my-4">
                    <table class="table table-compact w-full">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>District</th>
                                <th>Member</th>
                                <th>Membership No.</th>
                                <th>Receipt No.</th>
                                <th>particulars</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Remarks</th>
                                <th>Item Amount</th>
                                <th>Receipt Amount</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        @forelse ($receipts as $fp)
                        <tbody class="border-b border-base-content border-opacity-30">
                            @foreach ($fp->feeItems as $fi)
                            <tr>
                                <td>
                                    @if ($loop->first)
                                        {{$fp->formatted_receipt_date}}
                                    @endif
                                </td>
                                <td>@if ($loop->first){{$fp->district->name}}@endif</td>
                                <td>
                                    @if ($loop->first)
                                        {{$fp->member->display_name}}
                                    @endif
                                </td>
                                <td>
                                    @if ($loop->first)
                                        {{$fp->member->membership_no}}
                                    @endif
                                </td>
                                <td>
                                    @if ($loop->first)
                                    <a class="underline text-success" href="" @click.prevent.stop="$dispatch('linkaction', {link: '{{route('feecollections.show', $fp->id)}}', route: 'feecollections.show'})">
                                        {{$fp->receipt_number}}
                                    </a>
                                    @endif
                                </td>
                                <td>{{$fi->feeType->name}}</td>
                                <td>{{$fi->my_period_from ?? '--'}}</td>
                                <td>{{$fi->my_period_to ?? '--'}}</td>
                                <td>{{$fi->tenure ?? '--'}}</td>
                                <td>{{$fi->amount ?? ''}}</td>
                                <td>@if ($loop->first){{$fp->total_amount}}@endif</td>
                                <td>@if ($loop->first){{$fp->collectedBy != null ? $fp->collectedBy->name : '-'}}@endif</td>
                            </tr>
                            @endforeach
                        </tbody>
                        @empty
                        <tbody>
                            <tr>
                                <td class="text-error text-center" colspan="8">
                                    There are no transactions for this member.
                                </td>
                            </tr>
                        </tbody>
                        @endforelse
                    </table>
                </div>
                {{$receipts->appends(\Request::except('x_mode'))->links()}}
            </div>
            @endif
        </div>
    </div>
    <div x-show="showPrint" x-data="{
            showPrint: false,
            aggregates: [],
            receipts: [],
            fromdate: '',
            todate: '',
            reset() {
                this.showPrint = false;
                this.receipts = [];
                this.fromdate = '';
                this.todate = '';
            },
            doPrint() {
                let content = document.getElementById('receiptsprintdiv').innerHTML;
                let head = document.getElementsByTagName('head')[0].innerHTML;
                let w = window.open();
                w.document.write('<head>');
                w.document.write(head);
                w.document.write('</head>');
                w.document.write(content);
                setTimeout(() => {w.print(); w.close();}, 100);

            }
        }"
        @showreceiptsprint.window="
            aggregates = $event.detail.aggregates;
            receipts = $event.detail.receipts;
            fromdate = $event.detail.from;
            todate = $event.detail.to;
            console.log('receipts');
            console.log(receipts);
            showPrint = true;
            "
        class="fixed top-0 left-0 z-50 w-full h-full flex flex-row justify-center items-center bg-base-200 bg-opacity-50 overflow-visible"
        >
        <div class="max-w-full max-h-full md:w-11/12 relative bg-base-100 bg-opacity-100 border border-base-content border-opacity-20 rounded-lg p-4 pt-20 overflow-y-scroll overflow-visible">

            <div class="w-full text-right fixed top-10 right-20 z-50 flex flex-row justify-end space-x-4 print:hidden">
                <button @click="reset();" class="btn btn-error btn-sm">
                    Close <x-easyadmin::display.icon icon="easyadmin::icons.close"/>
                </button>
                <button @click="doPrint();" class="btn btn-warning btn-sm">
                    Print
                </button>
            </div>
            <div id="receiptsprintdiv">
                <h3 class="font-bold text-xl mb-4 mt-8 text-warning underline text-center">Fee Collections from <span x-text="fromdate"></span> to <span x-text="todate"></span></h3>
                <div class="flex justify-center my-12">
                    <div class="border border-base-content border-opacity-20 rounded-xl">
                    <h2 class="font-bold m-3">Fee Collection Summary:</h2>
                        <table class="table table-compact">
                            <thead>
                                <tr>
                                    <th>Fee Type</th>
                                    <th class="text-right">Total Count</th>
                                    <th class="text-right">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="a in Object.values(aggregates)">
                                <tr :class="a.ftname != 'Total' || 'font-bold'">
                                    <td x-text="a.ftname"></td>
                                    <td class="text-right" x-text="a.c_count"></td>
                                    <td class="text-right" x-text="a.c_sum"></td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <div class="mx-auto border border-base-content border-opacity-10 rounded-lg  my-4">
                        <table class="table table-compact w-full">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    {{-- <th>District</th> --}}
                                    <th>Member</th>
                                    <th>Member Reg. No.</th>
                                    <th>Receipt No.</th>
                                    <th>particulars</th>
                                    {{-- <th>From</th>
                                    <th>To</th> --}}
                                    <th>Tenure</th>
                                    <th>Amount</th>
                                    <th>Total Amount</th>
                                    <th>Notes</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <template x-for="r in receipts">
                            <tbody class="border-b border-base-content border-opacity-30">
                                <template x-for="(fi, index) in r.fee_items">
                                <tr>
                                    <td>
                                        <span x-show="index == 0" x-text="r.formatted_receipt_date"></span>
                                    </td>
                                    {{-- <td><span x-show="index == 0" x-text="r.district.name"></span></td> --}}
                                    <td>
                                        <span x-show="index == 0" x-text="r.member.name"></span>
                                    </td>
                                    <td>
                                        <span x-show="index == 0" x-text="r.member.membership_no"></span>
                                    </td>
                                    <td>
                                        <span x-show="index == 0" x-text="r.receipt_number"></span>
                                    </td>
                                    <td><span x-text="fi.fee_type.name"></span></td>
                                    {{-- <td>
                                        <span x-text="fi.period_from != null ? fi.period_from : '--'"></span>
                                    </td>
                                    <td>
                                        <span x-text="fi.period_to != null ? fi.period_to : '--'"></span>
                                    </td> --}}
                                    <td><span x-text="fi.tenure != null ? fi.tenure : '--'"></span></td>
                                    <td><span x-text="fi.amount"></td>
                                    <td><span x-show="index == 0" x-text="r.total_amount"></span></td>
                                    <td><span x-show="index == 0" x-text="r.notes"></span></td>
                                    <td><span x-show="index == 0" x-text="r.collected_by.name"></span></td>
                                </tr>
                                </template>
                            </tbody>
                            </template>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>
