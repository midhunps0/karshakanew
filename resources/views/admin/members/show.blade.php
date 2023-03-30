<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3"><span>Member's Profile</span>&nbsp;</h3>
        <div x-data="{
                activeTab: 0,
            }">
            <!--Tab Headings-->
            <div class="flex flex-row">
                <div @click="activeTab=0" tabindex="0" class="cursor-pointer font-bold border-t border-r border-l border-base-content border-opacity-10 w-1/3 p-3 rounded-tl-lg rounded-tr-lg" :class="activeTab != 0 || 'bg-base-200'">Personal</div>
                <div @click="activeTab=1" tabindex="0" class="cursor-pointer font-bold border-t border-r border-l border-base-content border-opacity-10 w-1/3 p-3 rounded-tl-lg rounded-tr-lg" :class="activeTab != 1 || 'bg-base-200'">Bank & Docs</div>
                <div @click="activeTab=2" tabindex="0" class="cursor-pointer font-bold border-t border-r border-l border-base-content border-opacity-10 w-1/3 p-3 rounded-tl-lg rounded-tr-lg" :class="activeTab != 2 || 'bg-base-200'">Transactions</div>
                <div @click="activeTab=3" tabindex="0" class="cursor-pointer font-bold border-t border-r border-l border-base-content border-opacity-10 w-1/3 p-3 rounded-tl-lg rounded-tr-lg" :class="activeTab != 3 || 'bg-base-200'">Allowances</div>
            </div>
            <!--Tab Contents-->
            <div>
                <div x-show="activeTab == 0" class="border-b border-r border-l border-base-content border-opacity-10 bg-base-200 min-h-48 rounded-b-lg">
                    <div class="flex flex-row flex-wrap items-start p-3">
                        <div class="md:w-1/3 p-1 my-1">
                            <span class="text-warning font-bold">Name:</span>&nbsp;
                            <span>{{$member->name}}</span>
                        </div>
                        <div class="md:w-1/3 p-1 my-1">
                            <span class="text-warning font-bold">Name in Malayalam:</span>&nbsp;
                            <span>{{$member->name_mal ?? '--'}}</span>
                        </div>
                        <div class="md:w-1/3 p-1 my-1">
                            <span class="text-warning font-bold">Date of birth:</span>&nbsp;
                            <span>{{$member->dob}}</span>
                        </div>
                        <div class="md:w-1/3 p-1 my-1">
                            <span class="text-warning font-bold">Gender:</span>&nbsp;
                            <span>{{$member->gender}}</span>
                        </div>
                        <div class="md:w-1/3 p-1 my-1">
                            <span class="text-warning font-bold">Marital Status:</span>&nbsp;
                            <span>{{$member->marital_status}}</span>
                        </div>
                        <div class="md:w-1/3 p-1 my-1">
                            <span class="text-warning font-bold">Parent/Guardian:</span>&nbsp;
                            <span>{{$member->parent_guardian}}</span>
                        </div>
                        <div class="md:w-1/3 p-1 my-1">
                            <span class="text-warning font-bold">Relationship with:</span>&nbsp;
                            <span>{{$member->guardian_relationship}}</span>
                        </div>
                        <div class="md:w-1/3 p-1 my-1">
                            <span class="text-warning font-bold">Mobile No.:</span>&nbsp;
                            <span>{{$member->mobile_no}}</span>
                        </div>
                    </div>
                    <div class="flex flex-row flex-wrap items-start p-3">
                        <div class="md:w-1/3 p-1 my-1">
                            <span class="text-warning font-bold">Permanent Address:</span><br>
                            <span>{{$member->permanent_address}}</span>
                        </div>
                        <div class="md:w-1/3 p-1 my-1">
                            <span class="text-warning font-bold">Permanent Address (In Malayalam):</span><br>
                            <span>{{$member->permanent_address_mal}}</span>
                        </div>
                        <div class="md:w-1/3 p-1 my-1">
                            <span class="text-warning font-bold">Permanent Address PIN:</span>&nbsp;
                            <span>{{$member->pa_pincode}}</span>
                        </div>
                        <div class="md:w-1/3 p-1 my-1">
                            <span class="text-warning font-bold">Current Address:</span><br>
                            <span>{{$member->current_address}}</span>
                        </div>
                        <div class="md:w-1/3 p-1 my-1">
                            <span class="text-warning font-bold">Current Address (In Malayalam):</span><br>
                            <span>{{$member->current_address_mal}}</span>
                        </div>
                        <div class="md:w-1/3 p-1 my-1">
                            <span class="text-warning font-bold">Current Address PIN:</span>&nbsp;
                            <span>{{$member->ca_pincode}}</span>
                        </div>
                    </div>
                    <div class="flex flex-row flex-wrap items-start p-3">
                        <div class="md:w-1/4 p-1 my-1">
                            <span class="text-warning">District:</span>
                            <span>{{$member->district->name}}</span>
                        </div>
                        <div class="md:w-1/4 p-1 my-1">
                            <span class="text-warning">Taluk:</span>
                            <span>{{$member->taluk->name}}</span>
                        </div>
                        <div class="md:w-1/4 p-1 my-1">
                            <span class="text-warning">Village:</span>&nbsp;
                            <span>{{$member->village->name}}</span>
                        </div>
                        <div class="md:w-1/4 p-1 my-1">
                            <span class="text-warning">Trade Union:</span>
                            <span>{{isset($member->trade_union) ? $member->trade_union->name : '--'}}</span>
                        </div>
                    </div>
                </div>
                <div x-show="activeTab == 1" class="border-b border-r border-l border-base-content border-opacity-10 bg-base-200 min-h-48 rounded-b-lg">
                        <div class="flex flex-row flex-wrap items-start p-3">
                            <div class="md:w-1/4 p-1 my-1">
                                <span class="text-warning">Bank:</span>
                                <span>{{$member->bank_name}}</span>
                            </div>
                            <div class="md:w-1/4 p-1 my-1">
                                <span class="text-warning">Bank IFSC:</span>
                                <span>{{$member->bank_ifsc}}</span>
                            </div>
                            <div class="md:w-1/4 p-1 my-1">
                                <span class="text-warning">Bank Branch:</span>&nbsp;
                                <span>{{$member->bank_branch}}</span>
                            </div>
                            <div class="md:w-1/4 p-1 my-1">
                                <span class="text-warning">Bank Account No.:</span>
                                <span>{{$member->bank_acc_no}}</span>
                            </div>
                        </div>
                        <div class="flex flex-row flex-wrap items-start p-3">
                            <div class="md:w-1/4 p-1 my-1">
                                <span class="text-warning">Aadhaar Card:</span>
                                <div class="block m-2 w-32 h-24">
                                    @if ($member->aadhaar_card != null)
                                    <img @click="$dispatch('showimg', {src: '{{$member->aadhaar_card['path']}}'});" class="cursor-pointer max-h-full max-w-full hover:scale-110 transition-transform" src="{{$member->aadhaar_card['path']}}" />
                                    @endif
                                </div>
                            </div>
                            <div class="md:w-1/4 p-1 my-1">
                                <span class="text-warning">Bank Passbook:</span>
                                <div class="block m-2 w-32 h-24">
                                    @if ($member->bank_passbook != null)
                                    <img @click="$dispatch('showimg', {src: '{{$member->bank_passbook['path']}}'});" class="cursor-pointer max-h-full max-w-full hover:scale-110 transition-transform" src="{{$member->bank_passbook['path']}}" />
                                    @endif
                                </div>
                            </div>
                        </div>

                </div>
                <div x-show="activeTab == 2" class="border-b border-r border-l border-base-content border-opacity-10 bg-base-200 min-h-48 rounded-b-lg">
                    <div class="md:max-w-3/4 mx-auto py-8 rounded-lg">
                        <table class="table table-compact w-full border border-base-content border-opacity-10">
                            <thead>
                                <th>Date</th>
                                <th>Receipt No.</th>
                                <th>particulars</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Tenure</th>
                                <th>Amount</th>
                                <th>Total Amount</th>
                            </thead>
                            @forelse ($member->feePayments as $fp)
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
                </div>
                <div x-show="activeTab == 3" class="border-b border-r border-l border-base-content border-opacity-10 bg-base-200 min-h-48 rounded-b-lg">
                    <div class="flex flex-row flex-wrap items-start">
                        Tab 3
                    </div>
                </div>
            </div>
        </div>
        <div x-show="showImg" x-transition x-data="{
                imgSrc: '',
                showImg: false
            }"
            @showimg.window="imgSrc = $event.detail.src; showImg = true;"
            class="fixed top-0 left-0 z-50 w-full h-full flex flex-row justify-center items-center bg-base-200 bg-opacity-50"
            >
            <div class="max-w-full max-h-full relative bg-base-100 bg-opacity-100 border border-base-content border-opacity-20 rounded-lg p-4">
                <button @click="showImg = false; imgSrc = '';" class="btn btn-error btn-sm absolute -top-14 -right-14">
                    <x-easyadmin::display.icon icon="easyadmin::icons.close"/>
                </button>
                <img :src="imgSrc" class="max-w-full max-h-full">
            </div>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>
