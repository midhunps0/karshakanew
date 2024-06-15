<x-easyadmin::partials.adminpanel>
    <div x-data="{
        searchBy: 'receipt_no',
        receiptNo: '',
        start: '',
        end: '',
        createdBy: '',
        page: 1,
        getParams() {
            let p = {
                searchBy: this.searchBy
            };
            if (this.searchBy != 'receipt_no' && this.start != '') {
                p['start'] = this.start;
            }
            if (this.searchBy != 'receipt_no' && this.end != '') {
                p['end'] = this.end;
            }
            if (this.searchBy == 'receipt_no') {
                p['receipt_no'] = this.receiptNo;
            }
            if (this.createdBy != '') {
                p['created_by'] = this.createdBy;
            }
            if (this.page != 1) {
                p['page'] = this.page;
            }
            return p;
        },
        fetchReport() {
            $dispatch('linkaction', {link: '{{route('feecollections.search')}}', route: 'feecollections.search', params: this.getParams(), fresh: true})
        },
        fetchPrintReport() {
            {{-- $dispatch('linkaction', {link: '{{route('feecollections.fullreport')}}', route: 'feecollections.report', params: this.getParams(), fresh: true, target: 'feecollection_report', history: false}) --}}
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
        @if (request()->get('receipt_no') != null)
            receiptNo = '{{request()->get('receipt_no')}}';
        @endif
        @if (request()->get('searchBy') != null)
            searchBy = '{{request()->get('searchBy')}}';
        @endif
        @if (request()->get('created_by') != null)
            createdBy = '{{request()->get('created_by')}}';
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
        <h3 class="text-xl font-bold pb-3">Search Receipts</h3>
        <div>
            <form action="" @submit.prevent.stop="page=1; fetchReport();">
                {{-- <div class="form-control w-full max-w-xs">
                    <label class="label">
                      <span class="label-text">Search BY</span>
                    </label>
                    <select class="select select-bordered flex-grow" x-model="searchBy" name="" id="">
                        <option value="receipt_no">Receipt No.</option>
                        <option value="date">Date</option>
                    </select>
                </div> --}}
                <div class="flex flex-row items-end space-x-4" >
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                          <span class="label-text">Search By</span>
                        </label>
                        <select class="select select-bordered flex-grow" x-model="searchBy" name="" id="">
                            <option value="created_at">Creation Date</option>
                            <option value="receipt_date">Receipt Date</option>
                            <option value="receipt_no">Receipt No.</option>
                        </select>
                    </div>
                    <div x-show="searchBy != 'receipt_no'" class="form-control w-full max-w-xs">
                        <label class="label justify-start space-x-2">
                          <span class="label-text">From </span><span x-show="searchBy == 'created_at'">Created Date</span><span x-show="searchBy == 'receipt_date'">Receipt Date</span>
                        </label>
                        <input x-model="start" type="text" name="start" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" :required="searchBy != 'receipt_no'"/>
                    </div>
                    <div x-show="searchBy != 'receipt_no'" class="form-control w-full max-w-xs">
                        <label class="label justify-start space-x-2">
                          <span class="label-text">To </span><span x-show="searchBy == 'created_at'">Created Date</span><span x-show="searchBy == 'receipt_date'">Receipt Date</span>
                        </label>
                        <input x-model="end" type="text" name="end" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" :required="searchBy != 'receipt_no'"/>
                    </div>
                    <div x-show="searchBy == 'receipt_no'" class="form-control w-full max-w-xs mr-4">
                        <label class="label">
                            <span class="label-text">Receipt No.</span>
                          </label>
                          <input x-model="receiptNo" type="text" name="end" class="input input-bordered w-full max-w-xs" :required="searchBy == 'receipt_no'"/>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <button type="submit" class="btn btn-md btn-success">Show Receipts</button>
                    </div>
                </div>
            </form>
            @if(count($receipts) > 0)
            <div>
                <div class="mx-auto border border-base-content border-opacity-10 rounded-lg max-h-[500px] overflow-scroll my-4">
                    <table class="table table-compact w-full">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Receipt No.</th>
                                <th>Membership No.</th>
                                <th>Member Name</th>
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
                                <td>
                                    @if ($loop->first)
                                        @if ($fp->is_editable_period || auth()->user()->hasPermissionTo('Fee Collection: Edit In Own District Any Time'))
                                        <div class="flex flex-row space-x-4 w-full items-center">
                                            <span>{{$fp->receipt_number}}</span>
                                            <a href="" @click.prevent.stop="$dispatch('linkaction', {
                                                link: '{{route('feecollections.show', $fp->id)}}', route: 'feecollections.show'
                                            });" class="flex flex-row space-x-4 items-center">
                                                <x-easyadmin::display.icon icon="easyadmin::icons.view_on" height="h-4" width="w-4" class="text-warning font-bold"/>
                                            </a>
                                            <a href="" @click.prevent.stop="$dispatch('linkaction', {
                                                link: '{{route('feecollections.edit', $fp->id)}}', route: 'feecollections.edit'
                                            });" class="flex flex-row space-x-4 items-center">
                                                <x-easyadmin::display.icon icon="easyadmin::icons.edit" height="h-4" width="w-4" class="text-warning font-bold"/>
                                            </a>
                                        </div>
                                        @else
                                            <span>{{$fp->receipt_number}}</span>
                                        @endif
                                    @endif</td>
                                <td>{{$fp->member->membership_no}}</td>
                                <td>{{$fp->member->display_name}}</td>
                                <td>{{$fi->feeType->name}}</td>
                                <td>{{$fi->feeType->name}}</td>
                                <td>{{$fi->formatted_period_from ?? '--'}}</td>
                                <td>{{$fi->formatted_period_to ?? '--'}}</td>
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
            @else
            <div class="text-center text-warning p-4">
                There are no receipts to show.
            </div>
            @endif
        </div>
    </div>
</x-easyadmin::partials.adminpanel>
