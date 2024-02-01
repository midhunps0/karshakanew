<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Chart Of Accounts ({{$districtName}}) </span>&nbsp;</h3>
        <div>
            @if (auth()->user()->district_id == 15)
            <form x-data="{
                districtId: null,
                doSubmit() {
                    let p = {
                        district_id: this.districtId
                    };
                    $dispatch('linkaction', {link: '{{route('accounts.chart')}}', route: 'accounts.chart', params: p});
                }
                }"
                action="" class=" flex flex-row space-x-4 items-end mb-8"
                @submit.prevent.stop="doSubmit();"
                x-init="
                    districtId = {{$districtId}};
                "
                >
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                      <span class="label-text">District</span>
                    </label>
                    <select x-model="districtId" class="select select-bordered select-sm py-0">
                        @foreach ($districts as $id => $name)
                        <option value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success btn-sm">Get Chart</button>
            </form>
            @endif
            @foreach ($accounts as $a)
                <div class="font-bold text-success">{{$a->name_with_district}}</div>
                @foreach ($a->subGroupsFamilyAccounts as $sgf)
                    <x-partials.account_group :sgf="$sgf" />
                    {{-- <div class="pl-5">{{$sgf->name}}</div>
                    @foreach ($sgf->subGroups as $sg)
                        <div class="pl-5">{{$sg->name}}</div>
                    @endforeach
                    @foreach ($sgf->accounts as $sga)
                        <div class="pl-5">{{$sga->name}}</div>
                    @endforeach --}}
                @endforeach

            @endforeach
        </div>
    </div>
</x-easyadmin::partials.adminpanel>
