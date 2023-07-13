<x-easyadmin::partials.adminpanel>
    <div x-data="{
        dateType: 'application_date',
        start: '',
        end: '',
        createdBy: null,
        status: null,
        page: 1,
        dowloadUrl: '',
        allColumns: [
            'application_date',
            'application_no',
            'member_name',
            'member_membership_no',
            'scheme_name',
            'sanctioned_amount',
            'sanctioned_date',
            'member_bank_account_bank_name',
            'member_bank_account_bank_branch',
            'member_bank_account_account_no',
            'member_bank_account_ifsc_code',
            'payment_date'
        ],
        selectedColumns: [
            'application_date',
            'application_no',
            'member_name',
            'member_membership_no',
            'scheme_name',
            'sanctioned_amount',
            'sanctioned_date',
            'member_bank_account_bank_name',
            'member_bank_account_bank_branch',
            'member_bank_account_account_no',
            'member_bank_account_ifsc_code',
            'payment_date'
        ],
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
            if (this.status != null) {
                p['status'] = this.status;
            }
            return p;
        },
        fetchReport() {
            $dispatch('linkaction', {link: '{{route('allowances.report')}}', route: 'allowances.report', params: this.getParams(), fresh: true})
        },
        fetchPrintReport() {
            $dispatch('linkaction', {link: '{{route('allowances.fullreport')}}', route: 'allowances.report', params: this.getParams(), fresh: true, target: 'allowances_report', history: false})
        },
        initPrint(data) {
            console.log(data);
            $dispatch('showreceiptsprint', {
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
        downloadUrl = '{{route('allowances.report.download').'?'}}';
        @if (request()->get('datetype') != null)
            dateType = '{{request()->get('datetype')}}';
            downloadUrl += 'datetype=' + dateType + '&';
        @endif
        @if (request()->get('created_by') != null)
            createdBy = '{{request()->get('created_by')}}';
            downloadUrl += 'created_by=' + createdBy + '&';
        @endif
        @if (request()->get('status') != null)
            status = '{{request()->get('status')}}';
            downloadUrl += 'status=' + status + '&';
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
            if($event.detail.target == 'allowances_report') {
                initPrint($event.detail.content);
            }
        "
    >
        <h3 class="text-xl font-bold pb-3">Allowances Report</h3>
        <div>
            <form action="" @submit.prevent.stop="page=1; fetchReport();">
                <div class="flex flex-row space-x-4 items-end justify-start my-4 w-full">
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                          <span class="label-text">Date Type</span>
                        </label>
                        <select class="select select-bordered flex-grow" x-model="dateType">
                            <option value="created_at">Creation Date</option>
                            <option value="application_date">Application Date</option>
                        </select>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                          <span class="label-text">From</span>
                        </label>
                        <input x-model="start" @change="formatDate($el, $event);" type="text" name="start" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]"/>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                          <span class="label-text">To</span>
                        </label>
                        <input x-model="end" type="text" name="end" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]"/>
                    </div>
                </div>
                <div class="flex flex-row space-x-4 items-end justify-start my-4 w-full">
                    @if ($user->hasPermissionTo('User: View In Any District') ||
                        $user->hasPermissionTo('User: View In Own District'))
                        <div class="form-control w-1/3 max-w-xs">
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
                    @endif
                    <div class="form-control w-1/3 max-w-xs">
                        <label class="label">
                        <span class="label-text">Status</span>
                        </label>
                        <select class="select select-bordered flex-grow" x-model="status">
                            <option value="">Any</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <div x-data="{
                            showList: false,
                        }" class="form-control w-1/3 max-w-xs relative">
                        <label class="label">
                        <span class="label-text">Columns</span>
                        </label>
                        <div class="w-full">
                            <input type="text" class="input input-md input-bordered rounded-md w-full" value="Choose Columns" readonly>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row w-full space-x-4 justify-start items-end my-4">
                    <div class="form-control w-full max-w-xs">
                        <button type="submit" class="btn btn-md btn-success">Get Report</button>
                    </div>
                    @if(count($allowances) > 0)
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

            <div class="flex flex-row flex-wrap justify-center items-start p-2">
                @if (count($allowances) > 0)
                    <div class="border border-base-content border-opacity-20 rounded-md min-w-1/2 mt-2 overflow-x-scroll">
                        <table class="table table-compact w-full">
                            <thead>
                                <tr>
                                    <th class="px-2">Appln. Date</th>
                                    <th class="px-2">Appln. No.</th>
                                    <th class="px-2">Member</th>
                                    <th class="px-2">Membership No.</th>
                                    <th class="px-2">Scheme Applied For</th>
                                    <th class="px-2">Status</th>
                                    {{-- <th class="px-2">Applied Amount</th> --}}
                                    <th class="px-2">Sanctioned Amount</th>
                                    <th class="px-2">Sanctioned Date</th>
                                    <th class="px-2">Payee Name</th>
                                    <th class="px-2">Bank & Branch</th>
                                    <th class="px-2">Account No.</th>
                                    <th class="px-2">IFSC COde</th>
                                    <th class="px-2">Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allowances as $a)
                                    <tr>
                                        <td class="px-2">{{$a->application_date}}</td>
                                        <td class="px-2">
                                            {{$a->application_no}}
                                            @if($a->allowanceable != null)
                                            <a href="" class="text-warning" @click.prevent.stop="$dispatch('linkaction', {link: '{{route('allowances.show', $a->id)}}', route: 'allowances.show'})">
                                                <x-easyadmin::display.icon icon="easyadmin::icons.view_on" height="h-4" width="w-4"/>
                                            </a>
                                            @endif
                                        </td>
                                        <td class="px-2">{{$a->member->name}}</td>
                                        <td class="px-2">{{$a->member->membership_no}}</td>
                                        <td class="px-2">{{$a->welfareScheme->name}}</td>
                                        <td class="px-2
                                        @if ($a->status == 'Pending') text-warning @endif
                                        @if ($a->status == 'Approved') text-primary @endif
                                        @if ($a->status == 'Paid') text-success @endif
                                        @if ($a->status == 'Rejected') text-error @endif
                                        ">{{$a->status}}</td>
                                        {{-- <td class="text-right px-2">{{$a->applied_amount}}</td> --}}
                                        <td class="text-right px-2">{{$a->sanctioned_amount}}</td>
                                        <td class="px-2">{{$a->sanctioned_date}}</td>
                                        <td class="text-left px-2">
                                            Payee: {{$a->allowanceable ? $a->allowanceable->member_bank_account['bank_name'] : '--'}}<br/>
                                            Bank & Branch: {{$a->allowanceable ? $a->allowanceable->member_bank_account['bank_branch'] : '--'}}<br/>
                                            Acc. No.: {{$a->allowanceable ? $a->allowanceable->member_bank_account['account_no'] : '--'}}<br/>
                                            IFSC: {{$a->allowanceable ? $a->allowanceable->member_bank_account['ifsc_code'] : '--'}}<br/>
                                        </td>
                                        <td class="px-2">{{$a->payment_date}}</td>
                                        <td class="px-2">{{$a->payment_date}}</td>
                                        <td class="px-2">{{$a->payment_date}}</td>
                                        <td class="px-2">{{$a->payment_date}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{$allowances->appends(\Request::except('x_mode'))->links()}}
                    </div>
                @elseif (isset($error))
                    <span class="text-error text-opacity-80">{{$error}}</span>
                @else
                    <span class="text-error text-opacity-80">No allowances till date.</span>
                @endif
            </div>
        </div>
    </div>
    <div x-show="showPrint" x-data="{
            showPrint: false,
            allowances: [],
            fromdate: '',
            todate: '',
            status: null,
            reset() {
                this.showPrint = false;
                this.receipts = [];
                this.fromdate = '';
                this.todate = '';
            },
            doPrint() {
                let content = document.getElementById('alowancesprintdiv').innerHTML;
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
            allowances = $event.detail.allowances;
            fromdate = $event.detail.from;
            todate = $event.detail.to;
            status = $event.detail.status;
            console.log('allowances');
            console.log(allowances);
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
            <div id="alowancesprintdiv">
                <h3 class="font-bold text-xl mb-4 mt-8 text-warning underline text-center">Allowance Applications from <span x-text="fromdate"></span> to <span x-text="todate"></span><span x-show="status != null" x-text="status "></span><span x-text="'\''+status+'\'.'"></span></h3>
                <div>
                    <div class="mx-auto border border-base-content border-opacity-10 rounded-lg  my-4">
                        <table class="table table-compact w-full">
                            <thead>
                                <tr>
                                    <th class="px-2">Appln. Date</th>
                                    <th class="px-2">Appln. No.</th>
                                    <th class="px-2">Scheme Applied For</th>
                                    <th class="px-2">Status</th>
                                    <th class="px-2">Applied Amount</th>
                                    <th class="px-2">Sanctioned Amount</th>
                                    <th class="px-2">Sanctioned Date</th>
                                    <th class="px-2">Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="a in allowances">
                                    <tr>
                                        <td class="px-2"><span x-text="a.application_date"></span></td>
                                        <td class="px-2">
                                            <span x-text="a.application_no"></span>
                                        </td>
                                        <td class="px-2">
                                            <span x-text="a.welfareScheme.name"></span>
                                        </td>
                                        <td class="px-2"><span x-text="a.status"></span></td>
                                        <td class="text-right px-2">
                                            <span x-text="a.applied_amount"></span>
                                        </td>
                                        <td class="text-right px-2">
                                            <span x-text="a.sanctioned_amount">
                                        </td>
                                        <td class="px-2">
                                            <span x-text="a.sanctioned_date">
                                        </td>
                                        <td class="px-2">
                                            <span x-text="a.payment_date">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>
