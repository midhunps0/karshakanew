<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3"><span>Search Member</span>&nbsp;</h3>
        <form x-data="{
                aadhaar_no: '',
                membership_no: '',
                name: '',
                district_id: null,
                taluk_id: null,
                village_id: null,
                search_by: 'Search Phrase',
                search_phrase: '',
                search_condition: 'st',
                taluks: [],
                villages: [],
                fetchTaluks() {
                    axios.get(
                        '{{route('district.taluks', '_X_')}}'.replace('_X_', this.district_id)
                    ).then((r) => {
                        let keys = Object.keys(r.data.taluks);
                        this.taluks = [];
                        keys.forEach((k) => {
                            this.taluks.push({
                                id: k,
                                name: r.data.taluks[k]
                            });
                        });
                        this.taluk_id = '';
                        this.village_id = '';
                    }
                    ).catch(
                        (e) => { console.log(e); }
                    );
                },
                fetchVillages() {
                    axios.get(
                        '{{route('taluks.villages', '_X_')}}'.replace('_X_', this.taluk_id)
                    ).then((r) => {
                        let keys = Object.keys(r.data.villages);
                        this.villages = [];
                        keys.forEach((k) => {
                            this.villages.push({
                                id: k,
                                name: r.data.villages[k]
                            });
                        });
                        this.village_id = '';
                    }
                    ).catch(
                        (e) => { console.log(e); }
                    );
                },
                getSearchBy() {
                    let str = '';
                    switch (this.search_by) {
                        case 'Aadhaar No.':
                            str = 'aadhaar_no';
                            break;
                        case 'Membership No.':
                            str = 'membership_no';
                            break;
                        default:
                            break;
                    }
                    return str;
                },
                doSubmit() {
                    //adv_search[]=membership_no::st::1
                    //filter[]=district_id::eq::3
                    //filter[]=taluk_id::eq::5
                    //filter[]=village_id::eq::5
                    url = '{{route('members.index')}}';
                    let querystr = '';
                    params = {};
                    if (this.village_id != null && this.village_id != '') {
                        querystr = '?adv_search[]=village::eq::' + this.village_id;
                    } else if (this.taluk_id != null &&  this.taluk_id != '') {
                        querystr = '?adv_search[]=taluk::eq::' + this.taluk_id;
                    } else if (this.district_id != null && this.district_id != '') {
                        querystr = '?adv_search[]=district::eq::' + this.district_id;
                    }
                    if (this.search_phrase != '') {
                        if (querystr != '') {
                            querystr += '&';
                        } else {
                            querystr = '?';
                        }
                        querystr += 'adv_search[]='+this.getSearchBy()+'::'+this.search_condition+'::' + this.search_phrase;
                    }
                    console.log('querystr');
                    console.log(querystr);
                    if (querystr != '') {
                        $dispatch('linkaction', {link: url+querystr, route: 'members.index'});
                    }
                }
            }
            "
            x-init="
            @if (isset($taluks))
                @foreach ($taluks as $id => $name)
                    taluks.push({
                        id: {{$id}},
                        name: '{{$name}}'
                    });
                @endforeach
            @endif
            "
            class="border border-base-content border-opacity-20 rounded-md py-12 shadow-md md:w-10/12 m-auto"
            @submit.prevent.stop="doSubmit()"
            >
            <div class="flex flex-row flex-wrap sm:flex-nowrap space-x-0 sm:space-x-4 sm:w-11/12 m-auto justify-center">
                <div class="form-control w-full sm:w-1/3 sm:max-w-xs">
                    <label class="label">
                        <span class="label-text">District</span>
                    </label>
                    <select x-model="district_id" @change.prevent.stop="fetchTaluks()" class="select select-md select-bordered"  @if (count($districts) == 1) disabled @endif>
                        @if (count($districts) > 1)
                            <option value="" selected>None</option>
                        @endif
                        @foreach ($districts as $id => $name)
                        <option @if (count($districts) == 1) selected @endif selected value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control w-full sm:w-1/3 sm:max-w-xs">
                    <label class="label">
                        <span class="label-text">Taluk</span>
                    </label>
                    <select x-model="taluk_id" @change.prevent.stop="fetchVillages()" class="select select-md select-bordered">
                        <option value="" selected>None</option>
                        <template x-for="t in taluks">
                            <option :value="t.id" x-text="t.name"></option>
                        </template>
                    </select>
                </div>
                <div class="form-control w-full sm:w-1/3 sm:max-w-xs">
                    <label class="label">
                        <span class="label-text">Village</span>
                    </label>
                    <select x-model="village_id" class="select select-md select-bordered">
                        <option value="" selected>None</option>
                        <template x-for="v in villages">
                            <option :value="v.id" x-text="v.name"></option>
                        </template>
                    </select>
                </div>
            </div>
            <div class="flex flex-row flex-wrap sm:flex-nowrap space-x-0 sm:space-x-4 sm:w-11/12 m-auto justify-center"
                >
                <div class="form-control w-full sm:w-1/3 sm:max-w-xs">
                    <label class="label">
                        <span class="label-text">What to search by?</span>
                    </label>
                    <select x-model="search_by" class="select select-md select-bordered">
                        <option selected value="Search Phrase">None</option>
                        <option value="Aadhaar No.">Aadhaar No.</option>
                        <option value="Membership No.">Membership No.</option>
                    </select>
                </div>
                <div class="form-control w-full sm:w-1/3 sm:max-w-xs">
                    <label class="label">
                      <span class="label-text" x-text="search_by"></span>
                    </label>
                    <input x-model="search_phrase" :disabled="search_by == 'Search Phrase'" type="text" placeholder="Type here" class="input input-md input-bordered" />
                </div>
                <div class="form-control w-full sm:w-1/3 sm:max-w-xs">
                    <label class="label">
                        <span class="label-text">Search Condition</span>
                    </label>
                    <select x-model="search_condition" :disabled="search_by == 'Search Phrase'" class="select select-md select-bordered">
                        <option selected value="st">Starts With</option>
                        <option value="ct">Contains</option>
                        <option value="en">Ends With</option>
                    </select>
                </div>
            </div>
            <div class="mt-8 py-4 text-center">
                <button type="submit" class="btn btn-md btn-primary px-8">Search</button>
            </div>
        </form>
    </div>
</x-easyadmin::partials.adminpanel>
