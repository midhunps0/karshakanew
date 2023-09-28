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
                doSUbmit(){}
            }" action="" @submit.prevent.stop="doSubmit();"
            class="m-auto max-w-1/2 rounded-md border border-base-content border-opacity-20 p-3"
            >
            @if ($member != null)
            <div class="opacity-80 mt-4 mb-8">
                    <span class="inline-block opacity-70 font-bold">Existing Member Name:</span><span class="inline-block font-bold">&nbsp;{{$member->display_name}}</span><br/>
                    <span class="inline-block opacity-70 font-bold">Membership No.:</span><span class="inline-block font-bold">&nbsp;{{$member->membership_no}}</span><br/>
                    <span class="inline-block opacity-70 font-bold">District</span><span class="inline-block font-bold">&nbsp;{{$member->district->name}}</span><br/>
                    <span class="inline-block opacity-70 font-bold">Taluk:</span><span class="inline-block font-bold">&nbsp;{{$member->taluk->name}}</span><br/>
                    <span class="inline-block opacity-70 font-bold">Village:</span><span class="inline-block font-bold">&nbsp;{{$member->village->name}}</span><br/>
            </div>
            @endif
            <div class="form-control w-full max-w-xs">
                <label class="label">
                <span class="label-text">Reg. No. from karshakathozhilali.org:</span>
                </label>
                <input type="text" placeholder="Reg. No." class="input input-bordered w-full max-w-xs" />
            </div>
            <div class="my-8 text-center">
                @if ($member != null)
                    <button type="submit" class="btn btn-warning btn-sm">Fetch & Sync Member</button>
                @else
                <button type="submit" class="btn btn-success btn-sm">Fetch & Add Member</button>
                @endif

            </div>
        </form>
    </div>
</x-easyadmin::partials.adminpanel>
