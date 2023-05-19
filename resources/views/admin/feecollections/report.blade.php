<x-easyadmin::partials.adminpanel>
    <div x-data="{
        dateType: 'receipt_date',
        start: '',
        end: '',
        page: 1,
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
            return p;
        },
        fetchReport() {
            $dispatch('linkaction', {link: '{{route('feecollections.report')}}', route: 'feecollections.report', params: this.getParams(), fresh: true})
        },
        fetchPrintReport() {
            $dispatch('linkaction', {link: '{{route('feecollections.fullreport')}}', route: 'feecollections.report', params: this.getParams(), fresh: true, target: 'feecollection_report', history: false})
        },
        initPrint(data) {
            console.log(data);
            $dispatch('showreceiptsprint', {
                receipts: data.receipts,
                from: this.start,
                to: this.end
            });
        }
    }"
    @pageaction="page = $event.detail.page; fetchReport();"
    x-init="
        @if (request()->get('datetype') != null)
            dateType = '{{request()->get('datetype')}}';
        @endif
        start = '{{request()->get('start') ?? ''}}';
        end = '{{request()->get('end') ?? ''}}';
        @if (request()->get('page') != null)
            page = {{request()->get('page')}};
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
                <div class="flex flex-row space-x-4 items-end">
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                          <span class="label-text">Date Type</span>
                        </label>
                        <select class="select select-bordered flex-grow" x-model="dateType" name="" id="">
                            <option value="created_at">Creation Date</option>
                            <option value="receipt_date">Receipt Date</option>
                        </select>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                          <span class="label-text">From</span>
                        </label>
                        <input x-model="start" type="text" name="start" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" required/>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                          <span class="label-text">To</span>
                        </label>
                        <input x-model="end" type="text" name="end" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" required/>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <button type="submit" class="btn btn-md btn-success">Get Report</button>
                    </div>
                    @if(count($receipts) > 0)
                    <div class="form-control w-full max-w-xs">
                        <button @click.prevent.stop="fetchPrintReport();" type="button" class="btn btn-md btn-warning">Print View</button>
                    </div>
                    @endif
                </div>
            </form>
            @if(count($receipts) > 0)
            <div>
                <div class="mx-auto border border-base-content border-opacity-10 rounded-lg h-96 overflow-scroll my-4">
                    <table class="table table-compact w-full">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Receipt No.</th>
                                <th>particulars</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Tenure</th>
                                <th>Amount</th>
                                <th>Total Amount</th>
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
                                <td>@if ($loop->first){{$fp->receipt_number}}@endif</td>
                                <td>{{$fi->feeType->name}}</td>
                                <td>{{$fi->period_from ?? '--'}}</td>
                                <td>{{$fi->period_to ?? '--'}}</td>
                                <td>{{$fi->tenure ?? '--'}}</td>
                                <td>{{$fi->amount ?? ''}}</td>
                                <td>@if ($loop->first){{$fp->total_amount}}@endif</td>
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
                <div>
                    <div class="mx-auto border border-base-content border-opacity-10 rounded-lg  my-4">
                        <table class="table table-compact w-full">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Receipt No.</th>
                                    <th>particulars</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Tenure</th>
                                    <th>Amount</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <template x-for="r in receipts">
                            <tbody class="border-b border-base-content border-opacity-30">
                                <template x-for="(fi, index) in r.fee_items">
                                <tr>
                                    <td>
                                        <span x-show="index == 0" x-text="r.formatted_receipt_date"></span>
                                    </td>
                                    <td>
                                        <span x-show="index == 0" x-text="r.receipt_number"></span>
                                    </td>
                                    <td><span x-text="fi.fee_type.name"></span></td>
                                    <td>
                                        <span x-text="fi.period_from != null ? fi.period_from : '--'"></span>
                                    </td>
                                    <td>
                                        <span x-text="fi.period_to != null ? fi.period_to : '--'"></span>
                                    <td>
                                        <span x-text="fi.tenure != null ? fi.tenure : '--'"></span></td>
                                    <td><span x-text="fi.amount"></td>
                                    <td><span x-show="index == 0" x-text="r.total_amount"></span></td>
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
