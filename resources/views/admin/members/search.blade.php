<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3"><span>Search Member</span>&nbsp;</h3>
        <form x-data="{
                search_string: '',
                membership_no: '',
                name: '',
                district_id: null,
                taluk_id: null,
                village_id: null,
                memNumDistrict: null,
                memNumTaluk: null,
                memNumVillage: null,
                memNumStr: null,
                search_by: 'Search Phrase',
                search_phrase: '',
                search_condition: 'is',
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
                        case 'Name':
                            str = 'name';
                            break;
                        case 'Name (Mal)':
                            str = 'name_mal';
                            break;
                        case 'Permanent Address':
                            str = 'permanent_address';
                            break;
                        case 'Permanent Address (Mal)':
                            str = 'permanent_address_mal';
                            break;
                        case 'Current Address':
                            str = 'current_address';
                            break;
                        case 'Current Address (Mal)':
                            str = 'current_address_mal';
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

                    if (this.search_by != 'Search Phrase') {
                        if (querystr != '') {
                            querystr += '&';
                        } else {
                            querystr = '?';
                        }
                        let searchStr = null;
                        /*
                        switch (this.getSearchBy()) {
                            case 'aadhaar_no':
                                searchStr = this.search_string;
                                break;
                            case 'membership_no':
                                searchStr = this.memNumDistrict + '/'
                                    + this.memNumTaluk + '/'
                                    + this.memNumVillage + '/'
                                    + this.memNumStr;
                                break;
                            case 'name':
                                searchStr = this.name;
                                break;
                        }*/

                        if (this.getSearchBy() == 'membership_no') {
                            searchStr = this.memNumDistrict + '/'
                                + this.memNumTaluk + '/'
                                + this.memNumVillage + '/'
                                + this.memNumStr;
                        } else {
                            searchStr = this.search_string;
                        }
                        querystr += 'adv_search[]='+this.getSearchBy()+'::'+this.search_condition+'::' + searchStr;
                    }

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
                        <option value="Name">Name</option>
                        <option value="Name (Mal)">Name (Mal)</option>
                        <option value="Permanent Address">Permanent Address</option>
                        <option value="Permanent Address (Mal)">Permanent Address (Mal)</option>
                        <option value="Current Address">Current Address</option>
                        <option value="Current Address (Mal)">Current Address (Mal)</option>
                    </select>
                </div>
                <div class="form-control w-full sm:w-1/3 sm:max-w-xs">
                    <label class="label">
                      <span class="label-text" x-text="search_by"></span>
                    </label>
                    <input x-show="search_by != 'Membership No.'" x-model="search_string" :disabled="search_by == 'Search Phrase'" type="text" placeholder="Type here" class="input input-md input-bordered" />
                    <div x-show="search_by == 'Membership No.'" class="w-full flex flex-row justify-between">
                        <input x-model="memNumDistrict" type="text" class="input input-md input-bordered w-16">
                        <input x-model="memNumTaluk" type="text" class="input input-md input-bordered w-16">
                        <input x-model="memNumVillage" type="text" class="input input-md input-bordered w-16">
                        <input x-model="memNumStr" type="text" class="input input-md input-bordered w-16">
                    </div>
                </div>
                <div class="form-control w-full sm:w-1/3 sm:max-w-xs">
                    <label class="label">
                        <span class="label-text">Search Condition</span>
                    </label>
                    <select x-model="search_condition" :disabled="search_by == 'Search Phrase'" class="select select-md select-bordered">
                        <option selected value="is">Exact</option>
                        <option value="st">Starts With</option>
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
