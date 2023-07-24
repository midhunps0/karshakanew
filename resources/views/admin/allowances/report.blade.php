<x-easyadmin::partials.adminpanel>
    <div x-data="{
        dateType: 'application_date',
        start: '',
        end: '',
        createdBy: null,
        status: null,
        scheme: null,
        course: null,
        allSchemeCols: {
            EDU: [{
                no: 101,
                title: 'Course',
                selected: true,
            }]
        },
        currSchemeCols: [],
        page: 1,
        dowloadUrl: '',
        allSchemes: [],
        allColumns: [
            {
                no: 0,
                title: 'Application Date',
                selected: true,
            },
            {
                no: 1,
                title: 'Application No.',
                selected: true,
            },
            {
                no: 2,
                title: 'Member Name',
                selected: true,
            },
            {
                no: 3,
                title: 'Membership No',
                selected: true,
            },
            {
                no: 4,
                title: 'Scheme Name',
                selected: true,
            },
            {
                no: 5,
                title: 'Status',
                selected: true,
            },
            {
                no: 6,
                title: 'Sanctioned Amount',
                selected: true,
            },
            {
                no: 7,
                title: 'Sanctioned Date',
                selected: true,
            },
            {
                no: 8,
                title: 'Payee Name',
                selected: true,
            },
            {
                no: 9,
                title: 'Bank & Branch',
                selected: true,
            },
            {
                no: 10,
                title: 'Account No.',
                selected: true,
            },
            {
                no: 11,
                title: 'IFSC Code',
                selected: true,
            },
            {
                no: 12,
                title: 'Payment Date',
                selected: true,
            },
            {
                no: 13,
                title: 'Created By',
                selected: true,
            },
            @if (auth()->user()->hasPermissionTo('Allowance: View In Any District'))
            {
                no: 14,
                title: 'District',
                selected: true,
            },
            @endif
        ],
        selectedColumns: [],
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
            if (this.scheme != null) {
                p['scheme'] = this.scheme;
            }
            if (this.course != null) {
                p['course'] = this.course;
            }
            p['cls'] = this.selectedColumns.length > 0 ? this.selectedColumns.join('|') : '';

            return p;
        },
        fetchReport() {
            $dispatch('linkaction', {link: '{{route('allowances.report')}}', route: 'allowances.report', params: this.getParams(), fresh: true})
        },
        fetchPrintReport() {
            $dispatch('linkaction', {link: '{{route('allowances.fullreport')}}', route: 'allowances.report', params: this.getParams(), fresh: true, target: 'allowances_report', history: false})
        },
        initPrint(data) {
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
        },
        getSchemeCode(id = null) {
            if (id == null) { id = this.scheme; }
            let s = this.allSchemes.filter((a) => {
                return a.id == id;
            })[0];
            return s != undefined && s != null ? s.code : '';
        }
    }"
    @pageaction="page = $event.detail.page; fetchReport();"
    x-init="
        $watch('allColumns', (v) => {
            selectedColumns = allColumns.filter((c) => {
                return c.selected == true;
            }).map((s) => {
                return s.no;
            });
        });
        @if (request()->get('datetype') != null)
            dateType = '{{request()->get('datetype')}}';
        @endif
        @if (request()->get('created_by') != null)
            createdBy = '{{request()->get('created_by')}}';
        @endif
        @if (request()->get('status') != null)
            status = '{{request()->get('status')}}';
        @endif
        @if (request()->get('scheme') != null)
            scheme = '{{request()->get('scheme')}}';
        @endif
        start = '{{request()->get('start') ?? ''}}';
        end = '{{request()->get('end') ?? ''}}';
        @if (request()->get('page') != null)
            page = {{request()->get('page')}};
        @endif
        @foreach ($schemes as $s)
            allSchemes.push({
                id: {{$s->id}},
                name: '{{$s->name}}',
                code: '{{$s->code}}'
            });
        @endforeach
        if (scheme != null && getSchemeCode() != '') {
            if (allSchemeCols[getSchemeCode()] != undefined) {
            allColumns.push(
                ...(allSchemeCols[getSchemeCode()])
            );
            }
        }
        let c = '{{$cols}}';
        if (c.lenght > 0) {
            selectedColumns = c.split('|').map((x) => {
                return 1 * x;
            });
        } else {
            selectedColumns = allColumns.map((ac) => {
                return ac.no;
            });;
        }
        if (selectedColumns.length > 0) {
            allColumns.forEach((ac) => {
                ac.selected =selectedColumns.includes(ac.no);
            });
        }

        downloadUrl = '{{route('allowances.report.download').'?'}}';
        let ps = getParams();
        let qArr = [];
        Object.keys(ps).forEach((k) => {
            qArr.push(k + '=' + ps[k]);
        });
        downloadUrl += qArr.join('&');
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
                        <input x-model="start" @change="formatDate($el, $event);" type="text" name="start" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                          <span class="label-text">To</span>
                        </label>
                        <input x-model="end" type="text" name="end" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                    </div>
                </div>
                <div class="flex flex-row space-x-4 items-end justify-start my-4 w-full">
                    @if ($user->hasPermissionTo('Allowance: View In Any District') ||
                        $user->hasPermissionTo('Allowance: View In Any District'))
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
                    <div class="form-control w-1/3 max-w-xs">
                        <label class="label">
                        <span class="label-text">Schemes</span>
                        </label>
                        <select class="select select-bordered flex-grow" x-model="scheme">
                            <option value="">Any</option>
                            @foreach ($schemes as $s)
                                <option value="{{$s->id}}">{{$s->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex flex-row space-x-4 items-end justify-start my-4 w-full">
                    <div x-show="getSchemeCode() == 'EDU'" class="form-control w-1/3 max-w-xs">
                        <label class="label">
                        <span class="label-text">Courses</span>
                        </label>
                        <select class="select select-bordered flex-grow" x-model="course">
                            <option value="">Any</option>
                            <option value="SSLC">SSLC</option>
                            <option value="THSLC">THSLC</option>
                            <option value="Plus2">Plus 2</option>
                            <option value="VHSE">VHSE</option>
                        </select>
                    </div>
                </div>
                <div x-data="{
                    showList: false,
                }" class="form-control w-1/3 max-w-xs relative">
                <label class="label">
                <span class="label-text">Columns</span>
                </label>
                <div class="w-full">
                    <input type="text" class="input input-md input-bordered rounded-md w-full" :value="selectedColumns.length + ' of ' + allColumns.length + ' columns selected'" readonly @click.prevent.stop="showList = !showList;">
                </div>
                <div @click.outside="showList = false;" x-show="showList" class="absolute z-10 top-20 left-0 bg-base-200 p-3 rounded-md bordered max-h-60 overflow-y-scroll">
                    <template x-for="c in allColumns">
                        <button type="button" class="block w-full text-left px-3 py-2 border-b border-base-300 border-opacity-50">
                            <input type="checkbox" x-model="c.selected" class="checkbox checkbox-sm checkbox-primary">
                            <span x-text="c.title"></span>
                        </button>
                    </template>
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
                                    <th x-show="selectedColumns.includes(0)" class="px-2">Appln. Date</th>
                                    <th x-show="selectedColumns.includes(1)" class="px-2">Appln. No.</th>
                                    @if (auth()->user()->hasPermissionTo('Allowance: View In Any District'))
                                    <th x-show="selectedColumns.includes(14)"  class="px-2">District</th>
                                    @endif
                                    <th x-show="selectedColumns.includes(2)"  class="px-2">Member</th>
                                    <th x-show="selectedColumns.includes(3)"  class="px-2">Membership No.</th>
                                    <th x-show="selectedColumns.includes(4)"  class="px-2">Scheme Applied For</th>
                                    <th x-show="getSchemeCode() == 'EDU' && selectedColumns.includes(101)" class="px-2">Course</th>
                                    <th x-show="selectedColumns.includes(5)"  class="px-2">Status</th>
                                    {{-- <th class="px-2">Applied Amount</th> --}}
                                    <th x-show="selectedColumns.includes(6)"  class="px-2">Sanctioned Amount</th>
                                    <th x-show="selectedColumns.includes(7)"  class="px-2">Sanctioned Date</th>
                                    <th x-show="selectedColumns.includes(8)"  class="px-2">Payee Name</th>
                                    <th x-show="selectedColumns.includes(9)"  class="px-2">Bank & Branch</th>
                                    <th x-show="selectedColumns.includes(10)"  class="px-2">Account No.</th>
                                    <th x-show="selectedColumns.includes(11)" class="px-2">IFSC COde</th>
                                    <th x-show="selectedColumns.includes(12)" class="px-2">Payment Date</th>
                                    <th x-show="selectedColumns.includes(13)" class="px-2">Created By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allowances as $a)
                                    @php
                                        // $showRoute = match($a->allowanceable_type) {
                                        //     'App\Models\DeathExgraciaApplication' => 'allowances.postdeath.show',
                                        //     'App\Models\EducationSchemeApplication' => 'allowances.education.show',
                                        //     default => 'allowances.education.show'
                                        // };
                                        $showRoute = App\Helpers\AppHelper::getShowRoute($a);
                                    @endphp
                                    <tr>
                                        <td x-show="selectedColumns.includes(0)" class="px-2">{{$a->application_date}}</td>
                                        <td x-show="selectedColumns.includes(1)" class="px-2">
                                            {{$a->application_no}}
                                            @if($a->allowanceable != null)
                                            <a href="" class="text-warning" @click.prevent.stop="$dispatch('linkaction', {link: '{{route($showRoute, $a->id)}}', route: '{{$showRoute}}'})">
                                                <x-easyadmin::display.icon icon="easyadmin::icons.view_on" height="h-4" width="w-4"/>
                                            </a>
                                            @endif
                                        </td>
                                        @if (auth()->user()->hasPermissionTo('Allowance: View In Any District'))
                                        <td x-show="selectedColumns.includes(14)"  class="px-2">{{$a->district->name}}</td>
                                        @endif
                                        <td x-show="selectedColumns.includes(2)" class="px-2">{{$a->member->display_name}}</td>
                                        <td x-show="selectedColumns.includes(3)" class="px-2">{{$a->member->membership_no}}</td>
                                        <td x-show="selectedColumns.includes(4)" class="px-2">{{$a->welfareScheme->name}}</td>
                                        <td x-show="getSchemeCode() == 'EDU' && selectedColumns.includes(101)" class="px-2">{{ isset($a->allowanceable) && $a->allowanceable_type == 'App\Models\EducationSchemeApplication' ? $a->allowanceable->passed_exam_details['exam_name'] : ''}}</td>
                                        <td x-show="selectedColumns.includes(5)" class="px-2
                                        @if ($a->status == 'Pending') text-warning @endif
                                        @if ($a->status == 'Approved') text-primary @endif
                                        @if ($a->status == 'Paid') text-success @endif
                                        @if ($a->status == 'Rejected') text-error @endif
                                        ">{{$a->status}}</td>
                                        {{-- <td class="text-right px-2">{{$a->applied_amount}}</td> --}}
                                        <td x-show="selectedColumns.includes(6)" class="text-right px-2">{{$a->sanctioned_amount}}</td>
                                        <td x-show="selectedColumns.includes(7)" class="px-2">{{$a->sanctioned_date}}</td>
                                        @php
                                            $bankAccount = $showRoute == 'allowances.postdeath.show' ? 'applicant_bank_details' : 'member_bank_account';
                                        @endphp
                                        <td x-show="selectedColumns.includes(8)" class="text-left px-2">
                                            {{$a->allowanceable ? $a->allowanceable->$bankAccount['bank_name'] : '--'}}
                                        </td>
                                        <td x-show="selectedColumns.includes(9)" class="px-2">
                                            {{$a->allowanceable ? $a->allowanceable->$bankAccount['bank_branch'] : '--'}}
                                        </td>
                                        <td x-show="selectedColumns.includes(10)" class="px-2">
                                            {{$a->allowanceable ? $a->allowanceable->$bankAccount['account_no'] : '--'}}
                                        </td>
                                        <td x-show="selectedColumns.includes(11)" class="px-2">
                                            {{$a->allowanceable ? $a->allowanceable->$bankAccount['ifsc_code'] : '--'}}
                                        </td>
                                        <td x-show="selectedColumns.includes(12)" class="px-2">{{$a->payment_date}}</td>
                                        <td x-show="selectedColumns.includes(13)" class="px-2">{{$a->createdBy->name}}</td>
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
