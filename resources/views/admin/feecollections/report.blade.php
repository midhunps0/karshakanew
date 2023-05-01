<x-easyadmin::partials.adminpanel>
    <div x-data="{
        start: '',
        end: '',
        page: 1,
        getParams() {
            let p = {};
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
        }
    }"
    @pageaction="page = $event.detail.page; fetchReport();"
    x-init="
        start = '{{request()->get('start') ?? ''}}';
        end = '{{request()->get('end') ?? ''}}';
        @if (request()->get('page') != null)
            page = {{request()->get('page')}};
        @endif
    "
    >
        <h3 class="text-xl font-bold pb-3">Fee Collections Report</h3>
        <div>
            <form action="" @submit.prevent.stop="page=1; fetchReport();">
                <div class="flex flex-row space-x-4 items-end">
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                          <span class="label-text">From Date</span>
                        </label>
                        <input x-model="start" type="text" name="start" class="input input-bordered w-full max-w-xs" required/>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                          <span class="label-text">To Date</span>
                        </label>
                        <input x-model="end" type="text" name="end" class="input input-bordered w-full max-w-xs" required/>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <button type="submit" class="btn btn-md btn-success">Get Report</button>
                    </div>
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
                                        {{$fp->receipt_date}}
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
</x-easyadmin::partials.adminpanel>
