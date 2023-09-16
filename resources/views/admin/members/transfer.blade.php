<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold"><span>Transfer Member</span>&nbsp;</h3>
        <div>
            <div class="flex flex-row flex-wrap my-4">
                <div class="w-1/3">
                    <label class="label font-bold opacity-60">Name:</label>
                    <div class="px-1">{{$member->display_name}}</div>
                </div>
                <div class="w-1/3">
                    <label class="label font-bold opacity-60">Membership No.:</label>
                    <div class="px-1">{{$member->membership_no}}</div>
                </div>
                <div class="w-1/3">
                    <label class="label font-bold opacity-60">Aadhaar No.:</label>
                    <div class="px-1">{{$member->aadhaar_no}}</div>
                </div>
            </div>
            <div class="flex flex-row flex-wrap my-4">
                <div class="w-1/3">
                    <label class="label font-bold opacity-60">District:</label>
                    <div class="px-1">{{$member->district->name}}</div>
                </div>
                <div class="w-1/3">
                    <label class="label font-bold opacity-60">Taluk:</label>
                    <div class="px-1">{{$member->taluk->name}}</div>
                </div class="w-1/3">
                <div class="w-1/3">
                    <label class="label font-bold opacity-60">Village:</label>
                    <div class="px-1">{{$member->village->name}}</div>
                </div>
            </div>
            <h4 class="text-md font-bold mt-8 mb-3"><span>Transfer To</span>&nbsp;</h4>
            <form
                x-data="{
                    district: '',
                    taluk: '',
                    village: '',
                    taluks: [],
                    villages: [],
                    getTaluks() {
                        this.taluks = [];
                        this.villages = [];
                        this.taluk = '';
                        this.village = '';
                        let url = '{{route('district.taluks', '_X_')}}'.replace('_X_', this.district);
                        axios.get(
                            url
                        ).then((r) => {
                            Object.keys(r.data.taluks).forEach((t) => {
                                this.taluks.push(
                                    {
                                        id: t,
                                        name: r.data.taluks[t]
                                    }
                                );
                            });
                        })
                        .catch((e) => {
                            console.log(e);
                        });
                    },
                    getVillages() {
                        this.villages = [];
                        this.village = '';
                        let url = '{{route('taluks.villages', '_X_')}}'.replace('_X_', this.taluk);
                        axios.get(
                            url
                        ).then((r) => {
                            Object.keys(r.data.villages).forEach((t) => {
                                this.villages.push(
                                    {
                                        id: t,
                                        name: r.data.villages[t]
                                    }
                                );
                            });
                        })
                        .catch((e) => {
                            console.log(e);
                        });
                    },
                    doSubmit() {
                        let fd = new FormData($el);
                        $dispatch('formsubmit', {url: '{{route('membertransfers.store')}}', formData: fd, target: $el.id});
                    }
                }" action="" class="flex flex-row flex-wrap justify-evenly border border-base-200 rounded-md p-3"
                @submit.prevent.stop="doSubmit();"
                @formresponse.window="
                    console.log($event.detail);
                    if($event.detail.content.success) {
                        $dispatch('showtoast', {
                            message: 'Transfer request created.',
                            mode: 'success'
                        });
                        $dispatch('linkaction', {
                            link: '{{route('members.transfer_requests')}}?tab=1',
                            route: 'members.transfer_requests',
                            fresh: true
                        });
                    } else {
                        $dispatch('shownotice', {
                            message: 'Failed to create transfer request. Unexpected error.',
                            mode: 'error'
                        });
                    }
                "
                >
                <input type="hidden" name="member_id" value="{{$member->id}}">
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                      <span class="label-text">District</span>
                    </label>
                    <select x-model="district" name="district" class="select select-bordered" @change="getTaluks()">
                        <option value="" disabled selected>Pick one</option>
                        @foreach ($districts as $d)
                            @if ($d->id != $member->district->id)
                            <option value="{{$d->id}}">{{$d->name}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                      <span class="label-text">Taluk</span>
                    </label>
                    <select x-model="taluk" name="taluk" class="select select-bordered" @change="getVillages()">
                        <option value="" disabled selected>Pick one</option>
                        <template x-for="(t, index) in taluks">
                            <option :value="t.id" x-text="t.name"></option>
                        </template>
                    </select>
                </div>
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                      <span class="label-text">Village</span>
                    </label>
                    <select x-model="village" name="village" class="select select-bordered">
                        <option value="" disabled selected>Pick one</option>
                        <template x-for="(v, index) in villages">
                            <option :value="v.id" x-text="v.name"></option>
                        </template>
                    </select>
                </div>
                {{-- <div class="my-4">
                    <button type="submit" class="btn btn-warning btn-sm"
                        :disabled="district == '' || taluk == '' || village == ''">Place Request</button>
                </div> --}}
                <div class="mt-8 mb-4 text-center">
                    <button type="submit" class="btn btn-warning btn-sm"
                        :disabled="district == '' || taluk == '' || village == ''">Place Request</button><br><br>
                        <button type="button" class="btn btn-xs btn-ghost"
                            @click.prevent.stop="history.back();">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>
