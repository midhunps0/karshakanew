<x-easyadmin::partials.adminpanel>
    <div  x-data="{
        members: [],
        districts: [],
        district: '',
        taluks: [],
        taluk: '',
        villages: [],
        columns: [
            {name: 'Display name', value: 'display_name'},
            {name: 'Membership No.', value: 'membership_no'},
            {name: 'District', value: 'district.name'},
            {name: 'Taluk', value: 'taluk.name'},
            {name: 'Village', value: 'village.name'},
            {name: 'Mobile No.', value: 'mobile_no'},
            {name: 'Aadhaar No.', value: 'aadhaar_no'},
        ],
        column_options: [
            {name: 'Display name', value: 'display_name'},
            {name: 'Membership No.', value: 'membership_no'},
            {name: 'District', value: 'district.name'},
            {name: 'Taluk', value: 'taluk.name'},
            {name: 'Village', value: 'village.name'},
            {name: 'Mobile No.', value: 'mobile_no'},
            {name: 'Aadhaar No.', value: 'aadhaar_no'},
            {name: 'Date of birth', value: 'dob'},
            {name: 'Gender', value: 'gender'},
            {name: 'Marital Status', value: 'marital_status'},
            {name: 'Parent/Guardian', value: 'parent_guardian'},
            {name: 'Guardian Relationship', value: 'guardian_relationship'},
            {name: 'Current Address', value: 'display_current_address'},
            {name: 'Current PIN Code', value: 'ca_pincode'},
            {name: 'Permanent Address', value: 'display_permanent_address'},
            {name: 'Permanent PIN Code', value: 'pa_pincode'},
            {name: 'Bank Ac. No.', value: 'bank_acc_no'},
            {name: 'Name in bank', value: 'bank_name'},
            {name: 'bank Branch', value: 'bank_branch'},
            {name: 'IFSC', value: 'bank_ifsc'},
            {name: 'Trade Union', value: 'trade_union.name'},
            {name: 'Status', value: 'active'},
            {name: 'Regn. Date', value: 'reg_date'},
        ],
        village: '',
        active: 1,
        gender: '',
        dob_from: null,
        dob_to: null,
        page: 1,
        downloadLink: '',
        showDownload: false,
        getTaluks() {
            if(this.district == '') {
                return false;
            }
            let url = '{{route('district.taluks', '_X_')}}';
            url = url.replace('_X_', this.district);
            axios.get(
                url
            ).then((r) => {
                let obj = r.data.taluks;
                this.taluks = [];
                (Object.keys(obj)).forEach((k) => {
                    this.taluks.push({
                        id: k,
                        name: obj[k]
                    });
                });
            }).catch((e) => {
                console.log(e);
            });
        },
        getVillages() {
            if(this.taluk == '') {
                return false;
            }
            let url = '{{route('taluks.villages', '_X_')}}';
            url = url.replace('_X_', this.taluk);
            axios.get(
                url
            ).then((r) => {
                let obj = r.data.villages;
                this.villages = [];
                (Object.keys(obj)).forEach((k) => {
                    this.villages.push({
                        id: k,
                        name: obj[k]
                    });
                });
            }).catch((e) => {
                console.log(e);
            });
        },
        getColValues() {
            let r = this.columns.reduce((result, c) => {
                return result + ',' + c.value;
            }, '');
            return r.substr(1);
        },
        doSubmit() {
            let url = '{{route('members.report.status')}}';
            let searches = [];

            if (this.district != '') {
                searches.push('district::eq::'+this.district);
            }
            if (this.taluk != '') {
                searches.push('taluk::eq::'+this.taluk);
            }
            if (this.village != '') {
                searches.push('village::eq::'+this.village);
            }
            if (this.status != '') {
                searches.push('active::eq::'+this.active);
            }
            if (this.gender != '') {
                searches.push('gender::eq::'+this.gender);
            }
            let params = {searches: searches, columns: this.getColValues()};
            if (this.page != 1) {
                params.page = this.page;
            }
            $dispatch('linkaction', {link: '{{route('members.report.custom')}}', route: 'members.report.status', params: params, fresh: true});
            {{-- axios.get(
                url,
                {
                    params: {searches: searches}
                }
            ).then((r) => {
                console.log(r.data);
            }).catch((e) => {
                console.log(e);
            }); --}}
        },
        setDownloadLink() {
            let url = '{{route('members.download.status')}}';
            let searches = ['active::is::'+this.active];

            if (this.district != '') {
                searches.push('district::eq::'+this.district);
            }
            if (this.taluk != '') {
                searches.push('taluk::eq::'+this.taluk);
            }
            if (this.village != '') {
                searches.push('village::eq::'+this.village);
            }

            let queryStr = '?searches[]=' + searches.join('&searches[]=');
            this.downloadLink = url + queryStr;
        },
        getDisplayValue(m, c) {
            if(c.indexOf('.') == -1) {
                return m[c];
            }
            let ar = c.split('.');
            let x = m;
            ar.forEach((i) => {
                x = x[i];
            });
            return x;
        },
        setColumn(col) {
            if(this.isColumnIncluded(col.name)) {
                this.columns = this.columns.filter((c) => {
                    return c.name != col.name;
                });
            } else {
                this.columns.push(col);
            }
        },
        isColumnIncluded(name) {
            let status = false;
            this.columns.forEach((c) => {
                if (c.name == name) {
                    status = true;
                }
            });
            return status;
        }
    }"
    x-init="
        $watch('district', (d) => {
            getTaluks();
        });
        $watch('taluk', (d) => {
            getVillages();
        });
        @if (isset($data['results']))
            console.log('test');
            let results = {{ Js::from($data['results']) }};
            members = results.data;
        @endif
        districts = {{Js::From($districts)}};
        @if(isset($data['searches']) && isset($data['searches']['active']))
        active = '{{$data['searches']['active']}}';
        @endif
        @if(isset($data['searches']) && isset($data['searches']['district_id']))
        district = {{$data['searches']['district_id']}};
        @elseif(count($districts) == 1)
        district = districts[0].id;
        @endif
        @if(isset($data['searches']) && isset($data['searches']['taluk_id']))
        taluk = {{$data['searches']['taluk_id']}};
        @endif
        @if(isset($data['searches']) && isset($data['searches']['village_id']))
        village = {{$data['searches']['village_id']}};
        @endif
        @if(isset($data['searches']) && isset($data['searches']['gender']))
        gender = '{{$data['searches']['gender']}}';
        @endif
        setDownloadLink();
        @if (isset($data['results']) && count($data['results']) > 0)
        showDownload = true;
        @endif
    ">
        <h3 class="text-xl font-bold pb-3"><span>Members: Custom Report</span>&nbsp;</h3>
        <form
            action=""
            class="w-full m-auto border border-base-content border-opacity-20 rounded-md p-4"
            @pageaction.window="
                page = $event.detail.page;
                doSubmit();
            "
            @submit.prevent.stop="doSubmit();"
            >
            <div class="flex flex-row space-x-4 m-auto items-end my-4">
                <div class="max-w-32">
                    <label class="text-warning">District</label><br>
                    <select x-model="district" class="select select-sm py-0 select-bordered">
                        @if (count($districts) != 1)
                        <option selected value="">Any District</option>
                        @endif
                        <template x-for="d in districts">
                            <option :value="d.id" :selected="district == d.id"><span x-text="d.name"></span></option>
                        </template>
                    </select>
                </div>
                <div class="max-w-32">
                    <label class="text-warning">Taluk</label><br>
                    <select x-model="taluk" class="select select-sm py-0 select-bordered">
                        <option selected value="">Any Taluk</option>
                        <template x-for="t in taluks">
                            <option :value="t.id" :selected="taluk == t.id"><span x-text="t.name"></span></option>
                        </template>
                    </select>
                </div>
                <div class="max-w-32">
                    <label class="text-warning">Village</label><br>
                    <select x-model="village" class="select select-sm py-0 select-bordered">
                        <option selected value="">Any Village</option>
                        <template x-for="v in villages">
                            <option :value="v.id" :selected="village == v.id"><span x-text="v.name"></span></option>
                        </template>
                    </select>
                </div>
                <div class="max-w-32">
                    <label class="text-warning">Status</label><br>
                    <select x-model="active" name="active" class="select select-sm py-0 select-bordered">
                        <option value="">Any</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="max-w-32">
                    <label class="text-warning">Gender</label><br>
                    <select x-model="gender" name="gender" class="select select-sm py-0 select-bordered">
                        <option value="">Any</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-row space-x-4 items-end justify-center my-4">
                <div class="w-1/5">
                    <button type="submit" class="btn btn-sm btn-success">Get Report</button>
                </div>
            </div>
            <div class="flex flex-row space-x-4 items-center justify-between p-2 bg-base-200 rounded-md">
                <div x-data="{
                        showcols: false,
                    }" class="w-1/5 relative">
                    <button @click.prevent.stop="showcols = !showcols;" type="button" class="btn btn-sm">Set Columns</button>
                    <div @click.outside="showcols = false;" x-show="showcols" class="absolute top-10 left-0 z-20 bg-base-200 py-3 rounded-md border border-base-content border-opacity-20 max-h-72 overflow-y-scroll">
                        <template x-for="c in column_options">
                            <li class="flex flex-row items-center justify-between hover:bg-base-300 px-3">
                                <button class="flex-grow text-left py-3" type="button" @click.prevent.stop="setColumn(c);" x-text="c.name"></button>
                                <div :class="{ 'text-base-content opacity-20' : !isColumnIncluded(c.name), 'text-success' : isColumnIncluded(c.name)}">
                                    <x-easyadmin::display.icon  icon="easyadmin::icons.tick" height="h-5" width="w-5"/>
                                </div>
                            </li>
                        </template>
                    </div>
                </div>
                <div class="text-center p-2">
                    <a :href="downloadLink" x-show="showDownload" class="btn btn-link btn-sm text-warning normal-case" download>Download</a>
                </div>
            </div>
        </form>
        <div class="my-4">
            {{-- {{dd($data['results']->items)}} --}}
            @if (isset($data['results']))
            <div class="border border-base-content border-opacity-20 rounded-lg overflow-x-scroll">
                <table class="table table-compact w-full">
                    <thead>
                        <tr>
                            <template x-for="c in columns">
                            <th x-text="c.name"></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="m in members">
                        <tr>
                            <template x-for="c in columns">
                                <td x-text="getDisplayValue(m, c.value)"></td>
                            </template>
                        </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @if(isset($data['results']))
        {{$data['results']->links()}}
        @endif
    </div>
</x-easyadmin::partials.adminpanel>
