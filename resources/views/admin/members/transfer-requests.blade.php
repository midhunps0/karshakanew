<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold"><span>Transfer Requests</span>&nbsp;</h3>
        <div x-data="{
                currentTab: 0,
            }"
            x-init="
                @if (request()->input('tab') != null)
                    currentTab = {{request()->input('tab')}};
                @endif
            " class="flex flex-col my-8">
            <div class="flex flex-row -mb-0.5 z-10">
                <button type="button" @click.prevent.stop="currentTab = 0" class="border-t border-r border-l border-base-content border-opacity-20  rounded-t-md py-2 px-4 font-bold" :class="currentTab == 0 ? 'bg-base-200 border-b-base-200' : 'opacity-40'">Requests Received</button>
                <button type="button" @click.prevent.stop="currentTab = 1" class="border-t border-r border-l border-base-content border-opacity-20  rounded-t-md py-2 px-4 font-bold" :class="currentTab == 1 ? 'bg-base-200' : 'opacity-40'">Requests Placed</button>
            </div>
            <div class="border border-base-content border-opacity-20 rounded-b-md rounded-tr-md p-3 bg-base-200">
                <div x-show="currentTab == 0" class="border border-base-100 rounded-xl"
                    x-data="{
                        currentId: '',
                        showApprovalForm: false,
                        doApprove() {
                            this.showApprovalForm = false;
                            {{-- let params = {
                                id: this.currentId
                            } --}}
                            axios.post(
                                '{{route('members.transfer.approve', '_X_')}}'.replace('_X_', this.currentId),
                            ).then((r) => {
                                if(r.data.success) {
                                    $dispatch('showtoast', {
                                        mode: 'success',
                                        message: 'Transfer request approved.'
                                    });
                                    $dispatch('linkaction', {
                                        link: '{{route('members.transfer_requests')}}',
                                        route: 'members.transfer_requests',
                                        fresh: true
                                    });
                                }

                                this.currentId = '';
                                this.showApprovalForm = false;
                            })
                            .catch((e) => {
                                console.log(e.data.error);
                                $dispatch('showtoast', {
                                    mode: 'error',
                                    message: 'Failed to approve the request doe to some unexpected error.'
                                });
                            });

                        }
                    }"
                    >
                    <table class="table table-compact w-full !rounded-none">
                        <thead>
                            <tr>
                                <th>Member Name</th>
                                <th>Membersip No.</th>
                                <th>District From</th>
                                <th>Taluk From</th>
                                <th>Village From</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($received as $rr)
                            <tr>
                                <td class="rounded-bl-lg">{{$rr->member->name}}</td>
                                <td>{{$rr->member->membership_no}}</td>
                                <td>{{$rr->fromDistrict->name}}</td>
                                <td>{{$rr->fromTaluk->name}}</td>
                                <td>{{$rr->fromVillage->name}}</td>
                                <td class="rounded-br-lg">
                                    @if ($rr->processedby_id == null)
                                    <div class="flex flex-row space-x-4">
                                        <button class="btn btn-xs btn-success" type="button" @click.prevent.stop="currentId = {{$rr->id}}; showApprovalForm = true;" class="text-warning">
                                            Approve
                                        </button>
                                        {{-- <button class="btn btn-xs btn-error btn-outline" type="button" @click.prevent.stop="" class="text-error">
                                            Reject
                                        </button> --}}
                                    </div>
                                    @else
                                    <button class="btn btn-xs btn-outline !text-success" type="button" @click.prevent.stop="" class="text-error" disabled>
                                        Approved
                                    </button>
                                    @endif
                                </td class="rounded-b-lg">
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div x-show="showApprovalForm" class="fixed z-50 top-0 left-0 h-screen w-screen bg-base-200 bg-opacity-60 flex flex-row justify-center items-center">
                        <div class="p-4 rounded-md bg-base-300 border border-base-100 max-w-200">
                            <div class="text-center mb-8">Confirm Approval</div>
                            <div class="flex flex-row space-x-8 justify-center">
                                <button @click.prevent.stop="showApprovalForm = false;" type="button" class="btn btn-ghost btn-sm">Cancel</button>
                                <button @click.prevent.stop="doApprove();" type="button" class="btn btn-success btn-sm">Approve</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div x-show="currentTab == 1" class="border border-base-100 rounded-xl">
                    <table class="table table-compact w-full !rounded-none">
                        <thead>
                            <tr>
                                <th>Member Name</th>
                                <th>Membersip No.</th>
                                <th>District To</th>
                                <th>Taluk To</th>
                                <th>Village To</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($placed as $rp)
                            <tr>
                                <td class="rounded-bl-lg">{{$rp->member->name}}</td>
                                <td>{{$rp->member->membership_no}}</td>
                                <td>{{$rp->district->name}}</td>
                                <td>{{$rp->taluk->name}}</td>
                                <td>{{$rp->village->name}}</td>
                                <td>
                                    <span class="{{$rp->processedby_id != null ? 'text-success' : 'text-warning'}}">
                                        {{$rp->processedby_id != null ? 'Approved' : 'Pending'}}
                                    </span>
                                </td>
                                <td class="rounded-br-lg">
                                    @if ($rp->processed_date == null)
                                    <div class="flex flex-row space-x-4">
                                        <button type="button" @click.prevent.stop="$dispatch('linkaction', {link: '{{route('members.transfer.edit', $rp->id)}}', route: 'members.transfer.edit', fresh: true});" class="text-warning">
                                            <x-easyadmin::display.icon icon="easyadmin::icons.edit" width="w-4" height="h-4"/>
                                        </button>
                                        <button type="button" @click.prevent.stop="" class="text-error">
                                            <x-easyadmin::display.icon icon="easyadmin::icons.close" width="w-4" height="h-4"/>
                                        </button>
                                    </div>
                                    @else
                                    --
                                    @endif
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
