<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3">
            @if ($member != null)
                <span>Sync Member Data</span>&nbsp;
            @else
                <span>Add Member Data</span>&nbsp;
            @endif
        </h3>
        <form x-data="{
            dataloading: false,
            membership_no: '',
            existing_member: null,
                doSubmit(){
                    let fd = new FormData($el);
                    this.dataloading = true;
                    axios.post(
                        '{{route('members.fetch.old')}}',
                        fd
                    ).then((r) => {
                        console.log(r.data);
                        this.dataloading = false;
                        this.membership_no = '';
                        if (r.data.success) {
                            $dispatch('showtoast', {message: 'Member data sync completed successfully!', mode: 'success'});
                            this.existing_member = null;
                        } else {
                            if (r.data.exists) {
                                this.existing_member = r.data.member;
                            }
                            $dispatch('showtoast', {message: 'Member data sync failed. Please make sure that you have entered the correct membership number.', mode: 'error'});
                        }
                    })
                    .catch((e) => {
                        console.log(e);
                        this.dataloading = false;$dispatch('showtoast', {message: 'Member data sync failed due to unexpected error.', mode: 'error'});
                    });
                }
            }" action="" @submit.prevent.stop="doSubmit();"
            class="m-auto max-w-1/2 rounded-md border border-base-content border-opacity-20 p-3"
            >
            @if ($status != 'ok')
                <div class="text-warning text-center p-4 rounded-lg my-2">
                    {{$status}}
                </div>
            @endif
            <div x-show="existing_member != null" class="border border-warning border-opacity-50 p-3 rounded-lg leading-relaxed">
                A member with the membership number you entered already exists.<br/>
                <span class="font-bolg text-warning">Membership No.:</span>&nbsp;<span x-text="existing_member.membership_no" class="font-bold text-warning"></span><br/>
                Please make sure you have entered the correct membership number.<br/>
                To sync data for existing members, please go to the corresponding member profile page.
            </div>
            @if ($member != null)
            <div class="opacity-80 mt-4 mb-8">
                    <span class="inline-block opacity-70 font-bold">Existing Member Name:</span><span class="inline-block font-bold">&nbsp;{{$member->display_name}}</span><br/>
                    <span class="inline-block opacity-70 font-bold">Membership No.:</span><span class="inline-block font-bold">&nbsp;{{$member->membership_no}}</span><br/>
                    <span class="inline-block opacity-70 font-bold">District</span><span class="inline-block font-bold">&nbsp;{{$member->district->name}}</span><br/>
                    <span class="inline-block opacity-70 font-bold">Taluk:</span><span class="inline-block font-bold">&nbsp;{{$member->taluk->name}}</span><br/>
                    <span class="inline-block opacity-70 font-bold">Village:</span><span class="inline-block font-bold">&nbsp;{{$member->village->name}}</span><br/>
            </div>
            <input type="hidden" name="member_id" value="{{$member->id}}">
            @endif
            <div class="form-control w-full max-w-xs">
                <label class="label">
                <span class="label-text">Reg. No. from karshakathozhilali.org:</span>
                </label>
                <input name="membership_no" x-model="membership_no" type="text" placeholder="Reg. No." class="input input-bordered w-full max-w-xs" />
            </div>
            <div class="my-8 text-center">
                @if ($member != null)
                    <button type="submit" class="btn btn-warning btn-sm">Fetch & Sync Member</button>
                @else
                <button type="submit" class="btn btn-success btn-sm">Fetch & Add Member</button>
                @endif

            </div>
            <div x-show="dataloading" class="fixed z-50 top-0 left-0 h-screen w-screen flex flex-row justify-center items-center bg-base-200 bg-opacity-50">
                <div class="bg-base-300 bg-opacity-100 p-4 rouded-lg text-center text-warning font-bold">
                    The system is fetching and syncing data. Please wait.<br>
                    Please don't refresh the page or press back button.<br>
                    <img class="h-8 inline-block m-auto my-4" src="/images/loading.gif" alt="">

                </div>
            </div>
        </form>
    </div>
</x-easyadmin::partials.adminpanel>
