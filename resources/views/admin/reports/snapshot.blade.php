<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3">Monthly Snapshot</h3>
        <div x-data="{
                years: [],
                year: '',
                month: '',
                districtId: null,
                allDistricts: [],
                fetchData() {
                    let theLink = `{{route('snapshot.report')}}?year=${this.year}&month=${this.month}`;
                    if (this.districtId != null) {
                        theLink += `&district=${this.districtId}`;
                    }
                    $dispatch('linkaction', {route: 'snapshot.report', link: theLink});
                }
            }"
            x-init="
                years = {{Js::from($years)}};
                year = {{Js::from($year)}};
                month = {{Js::from($month)}};
                districts = {{Js::from($districts)}};
                districtId = {{Js::from($districtId)}};
                console.log('districtId inside snapshot: '+districtId);
            ">
            <form action="" @submit.prevent.stop="fetchData();">
                <div class="flex flex-row space-x-4 items-end p-3 border border-base-content border-opacity-10 rounded-md">
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                        <span class="label-text">Month</span>
                        </label>
                        <select class="select select-bordered flex-grow" x-model="month">
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                        <span class="label-text">Year</span>
                        </label>
                        <select class="select select-bordered flex-grow" x-model="year">
                            <template x-for="y in years">
                                <option :value="y" :selected="year == y"><span x-text="y"></span></option>
                            </template>
                        </select>
                    </div>
                    @if(auth()->user()->hasPermissionTo('Fee Collection: View In Any District'))
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                        <span class="label-text">District</span>
                        </label>
                        <select class="select select-bordered flex-grow" x-model="districtId">
                            <option value="">All</option>
                            <template x-for="d in districts">
                                <option :value="d.id" :selected="districtId == d.id"><span x-text="d.name"></span></option>
                            </template>
                        </select>
                    </div>
                    @endif
                    <div>
                        <button type="submit" class="btn btn-success btn-sm">
                            Get Snapshot
                        </button>
                    </div>
                </div>
            </form>
            <div class="mt-8">
                <h1 class="font-bold text-md text-warning">Collections Data</h1>
                <div class="my-4 rounded-md border border-base-content border-opacity-20 overflow-hidden">
                    <table class="table-fixed">
                        <thead>
                            <tr class="bg-base-200">
                                <th class="p-3 w-48 text-left">Particulars</th>
                                <th class="p-3 w-28">Total Members Count</th>
                                <th class="p-3 w-28">Renewals for the month</th>
                                <th class="p-3 w-28">Renewals in previous year same month</th>
                                <th class="p-3 w-28">Collections for the month</th>
                                <th class="p-3 w-28">Collections in previous year same month</th>
                                <th class="p-3 w-28">Kuddisika Collection</th>
                                <th class="p-3 w-28">Kudissika Fine</th>
                                <th class="p-3 w-28">Members Remitted Kuddisika</th>
                                <th class="p-3 w-28">Total Collections</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="">
                                <td class="p-3 py-6">Count</td>
                                <td class="p-3 py-6 text-center">{{$collections['count']['members'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['count']['renewals'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['count']['renewals_previous'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['count']['collections'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['count']['collections_previous'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['count']['kudissika'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['count']['kudissika_fine'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['count']['kudissika_members'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['count']['collections'] ?? '--'}}</td>
                            </tr>
                            <tr class="">
                                <td class="p-3 py-6">Amount</td>
                                <td class="p-3 py-6 text-center">{{$collections['amount']['members'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['amount']['renewals'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['amount']['renewals_previous'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['amount']['collections'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['amount']['collections_previous'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['amount']['kudissika'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['amount']['kudissika_fine'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['amount']['kudissika_members'] ?? '--'}}</td>
                                <td class="p-3 py-6 text-center">{{$collections['amount']['collections'] ?? '--'}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-8">
                <h1 class="font-bold text-md text-warning">Allowances Data</h1>
                <div class="my-4 rounded-md border border-base-content border-opacity-20 overflow-hidden">
                    <table class="table-fixed">
                        <thead>
                            <tr class="bg-base-200">
                                <th class="p-3 w-52 text-left">Particulars</th>
                                <th class="p-3 w-36">Pending Applications Count</th>
                                <th class="p-3 w-36">Paid Allowances Count</th>
                                <th class="p-3 w-36">Paid Allowances Amount</th>
                                <th class="p-3 w-36">Total Paid Allowances Count</th>
                                <th class="p-3 w-36">Total Paid Allowances Amount</th>
                                <th class="p-3 w-36">Rejected Allowances Count</th>
                                <th class="p-3 w-36">Balance Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allowances as $row)
                                <tr @if($loop->last) class="font-bold text-warning" @endif>
                                    <td class="p-3">
                                        {{$row['name']}}
                                    </td>
                                    <td class="text-center p-3">
                                        {{$row['pending'] ?? '--'}}
                                    </td>
                                    <td class="text-center p-3">
                                        {{$row['paid_count'] ?? '--'}}
                                    </td>
                                    <td class="text-center p-3">
                                        {{$row['paid_amount'] ?? '--'}}
                                    </td>
                                    <td class="text-center p-3">
                                        {{$row['total_paid_count'] ?? '--'}}
                                    </td>
                                    <td class="text-center p-3">
                                        {{$row['total_paid_amount'] ?? '--'}}
                                    </td>
                                    <td class="text-center p-3">
                                        {{$row['rejected'] ?? '--'}}
                                    </td>
                                    <td class="text-center p-3">
                                        {{$row['balance'] ?? '--'}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>
