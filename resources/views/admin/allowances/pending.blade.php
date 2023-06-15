<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Pending Allowances</span>&nbsp;</h3>
        <div class="flex flex-row flex-wrap justify-center items-start p-2">
            @if (count($allowances) > 0)
            <div class="border border-base-content border-opacity-20 rounded-md min-w-1/2 mt-2 overflow-x-scroll">
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
                                <td class="px-2">{{$a->welfareScheme->name}}</td>
                                <td class="px-2
                                @if ($a->status == 'Pending') text-warning @endif
                                @if ($a->status == 'Approved') text-success @endif
                                @if ($a->status == 'Rejected') text-error @endif
                                ">{{$a->status}}</td>
                                <td class="text-right px-2">{{$a->applied_amount}}</td>
                                <td class="text-right px-2">{{$a->sanctioned_amount}}</td>
                                <td class="px-2">{{$a->sanctioned_date}}</td>
                                <td class="px-2">{{$a->payment_date}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <span class="text-error text-opacity-80">No allowances till date.</span>
            @endif
        </div>
    </div>
</x-easyadmin::partials.adminpanel>
