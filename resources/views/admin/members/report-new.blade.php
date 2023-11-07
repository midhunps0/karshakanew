<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3"><span>New Registrations Report</span>&nbsp;</h3>
        <form x-data="{
                districts: [],
                district: '',
                taluks: [],
                taluk: '',
                villages: [],
                village: '',
                from: '',
                to: '',
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
                formatDate(el, event) {
                    let re = /[0-9,-]/g;
                    let theval = el.value.replace('/', '-');
                    if (theval.length == 0) {
                        return false;
                    }
                    let x = (theval.match(re) || []).join('');
                    let arr = x.split('-');
                    let newarr = [];
                    for(i = 0; i < arr.length; i++) {
                        if (i < 2) {
                            newarr.push(arr[i].padStart(2, '0'));
                        } else {
                            newarr.push(arr[i]);
                        }
                    }
                    el.value = newarr.join('-');
                },
                formatDate(dt) {
                    let d = dt.split('-');
                    d = d.reverse();
                    return d.join('-');
                },
                doSubmit() {
                    let searches = [
                        'reg_date::gte::'+this.formatDate(this.from),
                        'reg_date::lte::'+this.formatDate(this.to),
                        ];

                    if (this.district != '') {
                        searches.push('district::eq::'+this.district);
                    }
                    if (this.taluk != '') {
                        searches.push('taluk::eq::'+this.taluk);
                    }
                    if (this.village != '') {
                        searches.push('village::eq::'+this.village);
                    }
                    let params = {searches: searches};
                    if (this.page != 1) {
                        params.page = this.page;
                    }
                    $dispatch('linkaction', {link: '{{route('members.report.new')}}', route: 'members.report.new', params: params, fresh: true});
                },
                setDownloadLink() {
                    let url = '{{route('members.download.new')}}';
                    let searches = [
                        'reg_date::gte::'+this.formatDate(this.from),
                        'reg_date::lte::'+this.formatDate(this.to),
                        ];

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
                }
            }"
            x-init="
                $watch('district', (d) => {
                    getTaluks();
                });
                $watch('taluk', (d) => {
                    getVillages();
                });
                districts = {{Js::From($districts)}};
                @if(isset($from))
                from = '{{$from}}';
                @endif
                @if(isset($to))
                to = '{{$to}}';
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
                setDownloadLink();
                @if (isset($data['results']) && count($data['results']) > 0)
                showDownload = true;
                @endif
            "
            action=""
            class="max-w-2/3 m-auto border border-base-content border-opacity-20 rounded-md p-4"
            @pageaction.window="
                page = $event.detail.page;
                doSubmit();
            "
            @submit.prevent.stop="doSubmit();"
            >
            <div class="flex flex-row space-x-4 m-auto items-end my-4">
                <div class="w-1/3">
                    <label>District</label>
                    <select x-model="district" class="select select-sm py-0 select-bordered w-full">
                        @if (count($districts) != 1)
                        <option selected value="">Any District</option>
                        @endif
                        <template x-for="d in districts">
                            <option :value="d.id" :selected="district == d.id"><span x-text="d.name"></span></option>
                        </template>
                    </select>
                </div>
                <div class="w-1/3">
                    <label>Taluk</label>
                    <select x-model="taluk" class="select select-sm py-0 select-bordered w-full">
                        <option selected value="">Any Taluk</option>
                        <template x-for="t in taluks">
                            <option :value="t.id" :selected="taluk == t.id"><span x-text="t.name"></span></option>
                        </template>
                    </select>
                </div>
                <div class="w-1/3">
                    <label>Village</label>
                    <select x-model="village" class="select select-sm py-0 select-bordered w-full">
                        <option selected value="">Any Village</option>
                        <template x-for="v in villages">
                            <option :value="v.id" :selected="village == v.id"><span x-text="v.name"></span></option>
                        </template>
                    </select>
                </div>
            </div>
            <div class="flex flex-row space-x-4 items-end justify-center my-4">
                <div class="w-1/3">
                    <label class="label">
                      <span class="label-text">Reg. Date: From</span>
                    </label>
                    <input x-model="from" @change="formatDate($el, $event);" type="text" name="start" class="input input-sm input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                </div>
                <div class="w-1/3">
                    <label class="label">
                      <span class="label-text">Reg. Date: To</span>
                    </label>
                    <input x-model="to" @change="formatDate($el, $event);" type="text" name="start" class="input input-sm input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                </div>
                <div class="w-1/3">
                    <button type="submit" class="btn btn-sm btn-success">Get Report</button>
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
                            <th class="!relative">Name</th>
                            <th>Membership No.</th>
                            <th>Aadhaar No.</th>
                            <th>Reg. date</th>
                            @if (count($districts) > 1)
                            <th>District</th>
                            @endif
                            <th>Taluk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['results'] as $m)
                        <tr>
                            <td>{{$m['display_name']}}</td>
                            <td>{{$m['membership_no']}}</td>
                            <td>{{$m['aadhaar_no']}}</td>
                            <td>{{$m['reg_date']}}</td>
                            @if (count($districts) > 1)
                            <td>{{$m['district']['name']}}</td>
                            @endif
                            <td>{{$m['taluk']['name']}}</td>
                        </tr>
                        @endforeach
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
